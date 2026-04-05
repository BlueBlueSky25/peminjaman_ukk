<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Alat;
use App\Models\User;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $alats = Alat::where('stok_tersedia', '>', 0)->get();
        
        // Untuk Peminjam: tampilkan history peminjamannya
        if ($user->level == 'peminjam') {
            $peminjaman = Peminjaman::where('user_id', $user->user_id)
                ->with(['alat', 'petugas'])
                ->latest()
                ->get();
            
            return view('pages.peminjaman.index-peminjam', compact('peminjaman', 'alats'));
        }
        
        // Untuk Admin & Petugas: tampilkan dashboard tracking
        $peminjamanMenunggu = Peminjaman::where('status', 'menunggu')
            ->with(['user', 'alat'])
            ->latest()
            ->get();
            
        $peminjamanAktif = Peminjaman::where('status', 'disetujui')
            ->with(['user', 'alat', 'petugas'])
            ->latest()
            ->get();
            
        $peminjamanSelesai = Peminjaman::where('status', 'dikembalikan')
            ->with(['user', 'alat', 'petugas','pengembalian'])
            ->latest()
            ->limit(10)
            ->get();
        
        return view('pages.peminjaman.index-petugas', compact('peminjamanMenunggu', 'peminjamanAktif', 'peminjamanSelesai'));
    }

    public function create()
    {
        $alats = Alat::where('stok_tersedia', '>', 0)->get();
        $users = User::where('level', 'peminjam')->get();
        return view('pages.peminjaman.create', compact('alats', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'alat_id' => 'required|exists:alat,alat_id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_peminjaman' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after:tanggal_peminjaman',
            'tujuan_peminjaman' => 'nullable|string',
        ]);

        $alat = Alat::findOrFail($request->alat_id);
        if ($alat->stok_tersedia < $request->jumlah) {
            return back()->withErrors(['jumlah' => 'Stok alat tidak mencukupi!']);
        }

        DB::transaction(function () use ($validated, $alat) {
            $validated['status'] = 'menunggu';
            Peminjaman::create($validated);
            $alat->decrement('stok_tersedia', $validated['jumlah']);
        });

        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Tambah Peminjaman',
            'modul' => 'Peminjaman',
            'timestamp' => now(),
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil diajukan!');
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
            'user_id' => Auth::id(),
            'aktivitas' => 'Update Status Peminjaman',
            'modul' => 'Peminjaman',
            'timestamp' => now(),
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
            'user_id' => Auth::id(),
            'aktivitas' => 'Hapus Peminjaman',
            'modul' => 'Peminjaman',
            'timestamp' => now(),
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil dihapus!');
    }

    public function approve(Request $request, Peminjaman $peminjaman)
    {
        $peminjaman->update([
            'status' => 'disetujui',
            'disetujui_oleh' => Auth::id(),
            'tanggal_disetujui' => now(),
        ]);

        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Menyetujui Peminjaman',
            'modul' => 'Peminjaman',
            'timestamp' => now(),
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil disetujui!');
    }
}