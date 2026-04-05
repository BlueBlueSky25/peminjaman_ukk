<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $userLevel = strtolower(auth()->user()->level);
        $userId = auth()->id();
        
        // Data KPI
        $totalUsers = User::count();
        $totalAlat = Alat::sum('stok_total');
        
        // Peminjaman - filter per user jika bukan admin/petugas
       if ($userLevel == 'admin' || $userLevel == 'petugas') {
    $peminjamanPending = Peminjaman::where('status', 'menunggu')->count();
    $peminjamanAktif = Peminjaman::where('status', 'disetujui')->count();
    $totalPengembalian = Pengembalian::count();
    // ✅ PERBAIKAN: Hanya hitung denda yang belum lunas
    $totalDenda = Pengembalian::where('status_denda', 'belum_lunas')->sum('total_denda');
    
    // ✅ PERBAIKAN: Alat yang masih dipinjam - exclude ditolak
    $alatMasihDipinjam = Peminjaman::with(['user', 'alat'])
        ->whereIn('status', ['disetujui', 'sebagian_kembali'])
        ->latest()
        ->get();
} else {
    // User biasa hanya lihat data mereka sendiri
    $peminjamanPending = Peminjaman::where('user_id', $userId)
        ->where('status', 'menunggu')
        ->count();
    
    $peminjamanAktif = Peminjaman::where('user_id', $userId)
        ->where('status', 'disetujui')
        ->count();
    
    // Pengembalian user - join dengan peminjaman
    $totalPengembalian = Pengembalian::whereHas('peminjaman', function($query) use ($userId) {
        $query->where('user_id', $userId);
    })->count();
    
    // ✅ PERBAIKAN: Total denda user yang belum lunas saja
    $totalDenda = Pengembalian::where('status_denda', 'belum_lunas')
        ->whereHas('peminjaman', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->sum('total_denda');
    
    // ✅ PERBAIKAN: Alat yang masih dipinjam - exclude ditolak & sebagian kembali
    $alatMasihDipinjam = Peminjaman::with(['user', 'alat'])
        ->where('user_id', $userId)
        ->whereIn('status', ['disetujui', 'sebagian_kembali'])
        ->latest()
        ->get();
}

        return view('pages.dashboard', compact(
            'totalUsers',
            'totalAlat',
            'peminjamanPending',
            'peminjamanAktif',
            'totalPengembalian',
            'totalDenda',
            'userLevel',
            'alatMasihDipinjam'
        ));
    }
}