<?php

namespace App\Http\Controllers;

use App\Models\Pengembalian;
use App\Models\Peminjaman;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengembalianController extends Controller
{
    public function index()
{
    // Ambil semua peminjaman yang belum dikembalikan sepenuhnya
    $peminjamanBelumSelesai = Peminjaman::with(['pengembalian', 'user', 'alat'])
        ->whereIn('status', ['disetujui', 'sebagian_kembali'])  // Exclude ditolak
        ->get()
        ->map(function($peminjaman) {
            $totalDikembalikan = $peminjaman->pengembalian()->sum('jumlah_dikembalikan') ?? 0;
            if ($totalDikembalikan < $peminjaman->jumlah) {
                return $peminjaman;
            }
        })
        ->filter();

    $pengembalian = Pengembalian::with('peminjaman.user', 'peminjaman.alat')->latest()->get();
    
    // PERBAIKAN: Hanya denda yang belum_lunas
    $dendaBelumLunas = Pengembalian::where('status_denda', 'belum_lunas')
        ->with(['peminjaman.user', 'peminjaman.alat'])
        ->latest()
        ->get();
    
    // PERBAIKAN: Exclude peminjaman yang ditolak
    $barangMasihDipinjam = Peminjaman::whereIn('status', ['disetujui', 'sebagian_kembali'])
        ->with(['user', 'alat', 'pengembalian'])
        ->latest()
        ->get();
    
    return view('pages.pengembalian.index', compact('pengembalian', 'peminjamanBelumSelesai', 'dendaBelumLunas', 'barangMasihDipinjam'));
}

   public function store(Request $request)
{
    $validated = $request->validate([
        'peminjaman_id' => 'required|exists:peminjaman,peminjaman_id',
        'tanggal_kembali_aktual' => 'required|date',
        'kondisi_baik' => 'required|numeric|min:0',
        'kondisi_rusak' => 'required|numeric|min:0',
        'kondisi_hilang' => 'required|numeric|min:0',
        'keterangan' => 'nullable|string',
    ]);

    // Paksa jadi integer
    $validated['kondisi_baik'] = (int) $validated['kondisi_baik'];
    $validated['kondisi_rusak'] = (int) $validated['kondisi_rusak'];
    $validated['kondisi_hilang'] = (int) $validated['kondisi_hilang'];
    

    $peminjaman = Peminjaman::findOrFail($request->peminjaman_id);
    
    $totalKembali = $validated['kondisi_baik'] + $validated['kondisi_rusak'] + $validated['kondisi_hilang'];

    if ($totalKembali === 0) {
        return back()->withErrors(['kondisi_baik' => 'Minimal ada 1 item yang dikembalikan']);
    }

    if ($totalKembali > $peminjaman->jumlah) {
        return back()->withErrors(['kondisi_baik' => 'Total kembali melebihi jumlah pinjaman']);
    }

    $tanggalKembali = Carbon::parse($request->tanggal_kembali_aktual);
    $jatuhTempo = Carbon::parse($peminjaman->tanggal_kembali_rencana);
    $keterlambatan = max(0, $tanggalKembali->diffInDays($jatuhTempo, false) * -1);

    $tarifDenda = 5000;
    $totalDenda = $keterlambatan * $tarifDenda * $totalKembali;

    DB::transaction(function () use ($validated, $peminjaman, $keterlambatan, $tarifDenda, $totalDenda, $totalKembali) {
        // TENTUKAN KONDISI MAYORITAS
        $kondisiAlat = 'baik';
        if ($validated['kondisi_hilang'] > 0) {
            $kondisiAlat = 'hilang';
        } elseif ($validated['kondisi_rusak'] > 0) {
            $kondisiAlat = 'rusak';
        }

        // Validasi enum sebelum insert
        $validKondisi = ['baik', 'rusak', 'hilang'];
        if (!in_array($kondisiAlat, $validKondisi)) {
            throw new \Exception('Kondisi alat tidak valid');
        }

        Pengembalian::create([
            'peminjaman_id' => $validated['peminjaman_id'],
            'tanggal_kembali_aktual' => $validated['tanggal_kembali_aktual'],
            'kondisi_alat' => $kondisiAlat,
            'keterlambatan_hari' => $keterlambatan,
            'tarif_denda_per_hari' => $tarifDenda,
            'total_denda' => $totalDenda,
            'status_denda' => $totalDenda > 0 ? 'belum_lunas' : 'lunas',
            'keterangan' => json_encode([
                'baik' => $validated['kondisi_baik'],
                'rusak' => $validated['kondisi_rusak'],
                'hilang' => $validated['kondisi_hilang'],
                'catatan' => $validated['keterangan']
            ]),
            'jumlah_dikembalikan' => $totalKembali,
        ]);

        $sisaPinjam = $peminjaman->jumlah - $totalKembali;
        
        if ($sisaPinjam == 0) {
            $peminjaman->update(['status' => 'dikembalikan']);
        } else {
            $peminjaman->update(['status' => 'sebagian_kembali', 'jumlah' => $sisaPinjam]);
        }

        $peminjaman->alat->increment('stok_tersedia', $validated['kondisi_baik']);
        $peminjaman->alat->increment('stok_rusak', $validated['kondisi_rusak']);
        $peminjaman->alat->increment('stok_hilang', $validated['kondisi_hilang']);
    });

    LogAktivitas::create([
        'user_id' => Auth::id(),
        'aktivitas' => 'Proses Pengembalian - ' . $peminjaman->kode_peminjaman,
        'modul' => 'Pengembalian',
        'timestamp' => now(),
    ]);

    return redirect()->route('pengembalian.index')->with('success', 'Pengembalian berhasil diproses!');
}

    // UPDATE: Verifikasi pembayaran denda
    public function verifikasiPembayaran(Request $request, $id)
    {
        $validated = $request->validate([
            'jumlah_bayar' => 'required|numeric|min:0',
            'tanggal_bayar_denda' => 'required|date',
            'metode_pembayaran' => 'nullable|string',
            'keterangan_pembayaran' => 'nullable|string',
        ]);

        $pengembalian = Pengembalian::findOrFail($id);
        
        // Cek apakah jumlah bayar sesuai dengan denda
        if ($validated['jumlah_bayar'] < $pengembalian->total_denda) {
            return back()->withErrors(['jumlah_bayar' => 'Jumlah pembayaran kurang dari total denda (Rp ' . number_format($pengembalian->total_denda, 0, ',', '.') . ')']);
        }

        $pengembalian->update([
            'status_denda' => 'lunas',
            'tanggal_bayar_denda' => $validated['tanggal_bayar_denda'],
            'jumlah_bayar' => $validated['jumlah_bayar'],
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'diverifikasi_oleh' => Auth::id(),
            'keterangan_pembayaran' => $validated['keterangan_pembayaran'],
        ]);

        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Verifikasi Pembayaran Denda - ' . $pengembalian->peminjaman->kode_peminjaman . ' (Rp ' . number_format($validated['jumlah_bayar'], 0, ',', '.') . ')',
            'modul' => 'Pengembalian',
            'timestamp' => now(),
        ]);

        return redirect()->route('pengembalian.index')->with('success', 'Pembayaran denda berhasil diverifikasi!');
    }

    public function destroy(Pengembalian $pengembalian)
    {
        $pengembalian->delete();

        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Hapus Pengembalian',
            'modul' => 'Pengembalian',
            'timestamp' => now(),
        ]);

        return redirect()->route('pengembalian.index')->with('success', 'Data pengembalian berhasil dihapus!');
    }
}