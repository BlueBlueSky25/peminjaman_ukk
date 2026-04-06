<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Alat;
use App\Models\User;
use App\Models\LogAktivitas;
use App\Mail\PeminjamanMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PeminjamanController extends Controller
{
    // ============================================================
    // PUBLIC - Form peminjaman tanpa login
    // ============================================================

    public function form()
    {
        $alats = Alat::where('stok_tersedia', '>', 0)->get();
        return view('pages.peminjaman.form-publik', compact('alats'));
    }

    public function storePublic(Request $request)
    {
        $validated = $request->validate([
            'nama_peminjam'          => 'required|string|max:255',
            'email'                  => 'required|email|max:255',
            'alat_id'                => 'required|exists:alat,alat_id',
            'jumlah'                 => 'required|integer|min:1',
            'tanggal_peminjaman'     => 'required|date|after_or_equal:today',
            'tanggal_kembali_rencana'=> 'required|date|after:tanggal_peminjaman',
            'tujuan_peminjaman'      => 'nullable|string',
        ]);

        $alat = Alat::findOrFail($request->alat_id);

        if ($alat->stok_tersedia < $request->jumlah) {
            return back()->withErrors(['jumlah' => 'Stok alat tidak mencukupi!'])->withInput();
        }

        // Generate kode peminjaman: PJM-YYYYMMDD-XXXX (random 4 huruf besar)
        $kode = 'PJM-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4));

        $peminjaman = null;

        DB::transaction(function () use ($validated, $alat, $kode, &$peminjaman) {
            $peminjaman = Peminjaman::create([
                'kode_peminjaman'         => $kode,
                'user_id'                 => null,
                'nama_peminjam'           => $validated['nama_peminjam'],
                'email'                   => $validated['email'],
                'alat_id'                 => $validated['alat_id'],
                'jumlah'                  => $validated['jumlah'],
                'tanggal_peminjaman'      => $validated['tanggal_peminjaman'],
                'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana'],
                'tujuan_peminjaman'       => $validated['tujuan_peminjaman'] ?? null,
                'status'                  => 'menunggu',
            ]);

            $alat->decrement('stok_tersedia', $validated['jumlah']);
        });

        // Kirim email struk ke peminjam
        Mail::to($validated['email'])->send(new PeminjamanMail($peminjaman));

        return redirect()->route('peminjaman.form')->with('success',
            'Peminjaman berhasil diajukan! Cek email kamu untuk struk dan kode peminjaman: <strong>' . $kode . '</strong>'
        );
    }

    // ============================================================
    // AUTH - Dashboard petugas & admin
    // ============================================================

    public function index(Request $request)
    {
        $search = $request->input('search');

        $peminjamanMenunggu = Peminjaman::where('status', 'menunggu')
            ->with(['user', 'alat'])
            ->when($search, function ($q) use ($search) {
                $q->where('kode_peminjaman', 'like', '%' . $search . '%')
                  ->orWhere('nama_peminjam', 'like', '%' . $search . '%');
            })
            ->latest()
            ->get();

        $peminjamanAktif = Peminjaman::where('status', 'disetujui')
            ->with(['user', 'alat', 'petugas'])
            ->when($search, function ($q) use ($search) {
                $q->where('kode_peminjaman', 'like', '%' . $search . '%')
                  ->orWhere('nama_peminjam', 'like', '%' . $search . '%');
            })
            ->latest()
            ->get();

        $peminjamanSelesai = Peminjaman::where('status', 'dikembalikan')
            ->with(['user', 'alat', 'petugas', 'pengembalian'])
            ->when($search, function ($q) use ($search) {
                $q->where('kode_peminjaman', 'like', '%' . $search . '%')
                  ->orWhere('nama_peminjam', 'like', '%' . $search . '%');
            })
            ->latest()
            ->limit(10)
            ->get();

        return view('pages.peminjaman.index-petugas', compact(
            'peminjamanMenunggu',
            'peminjamanAktif',
            'peminjamanSelesai',
            'search'
        ));
    }

    public function update(Request $request, Peminjaman $peminjaman)
    {
        $validated = $request->validate([
            'status' => 'required|in:menunggu,disetujui,ditolak,dikembalikan',
        ]);

        if ($request->status == 'disetujui' && $peminjaman->status != 'disetujui') {
            $validated['disetujui_oleh'] = Auth::id();
            $validated['tanggal_disetujui'] = now();
        }

        if ($request->status == 'ditolak' && $peminjaman->status != 'ditolak') {
            $peminjaman->alat->increment('stok_tersedia', $peminjaman->jumlah);
        }

        $peminjaman->update($validated);

        LogAktivitas::create([
            'user_id'    => Auth::id(),
            'aktivitas'  => 'Update Status Peminjaman',
            'modul'      => 'Peminjaman',
            'timestamp'  => now(),
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Status peminjaman berhasil diupdate!');
    }

    public function destroy(Peminjaman $peminjaman)
    {
        if ($peminjaman->status != 'dikembalikan') {
            $peminjaman->alat->increment('stok_tersedia', $peminjaman->jumlah);
        }

        $peminjaman->delete();

        LogAktivitas::create([
            'user_id'   => Auth::id(),
            'aktivitas' => 'Hapus Peminjaman',
            'modul'     => 'Peminjaman',
            'timestamp' => now(),
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil dihapus!');
    }

    public function approve(Request $request, Peminjaman $peminjaman)
    {
        $peminjaman->update([
            'status'          => 'disetujui',
            'disetujui_oleh'  => Auth::id(),
            'tanggal_disetujui' => now(),
        ]);

        LogAktivitas::create([
            'user_id'   => Auth::id(),
            'aktivitas' => 'Menyetujui Peminjaman',
            'modul'     => 'Peminjaman',
            'timestamp' => now(),
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil disetujui!');
    }
}