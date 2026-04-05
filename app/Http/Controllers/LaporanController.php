<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        // Peminjaman hari ini
        $peminjamanHariIni = Peminjaman::with(['user', 'alat'])
            ->whereDate('tanggal_peminjaman', $tanggal)
            ->get();

        // Pengembalian hari ini
        $pengembalianHariIni = Pengembalian::with(['peminjaman.user', 'peminjaman.alat'])
            ->whereDate('tanggal_kembali_aktual', $tanggal)
            ->get();

        $totalPeminjamanHariIni = $peminjamanHariIni->count();
        $totalPengembalianHariIni = $pengembalianHariIni->count();
        $totalDendaHariIni = $pengembalianHariIni->sum('total_denda');

        return view('pages.laporan.index', compact(
            'tanggal',
            'peminjamanHariIni',
            'pengembalianHariIni',
            'totalPeminjamanHariIni',
            'totalPengembalianHariIni',
            'totalDendaHariIni'
        ));
    }

    public function cetak(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        $peminjamanHariIni = Peminjaman::with(['user', 'alat'])
            ->whereDate('tanggal_peminjaman', $tanggal)
            ->get();

        $pengembalianHariIni = Pengembalian::with(['peminjaman.user', 'peminjaman.alat'])
            ->whereDate('tanggal_kembali_aktual', $tanggal)
            ->get();

        $totalPeminjamanHariIni = $peminjamanHariIni->count();
        $totalPengembalianHariIni = $pengembalianHariIni->count();
        $totalDendaHariIni = $pengembalianHariIni->sum('total_denda');

        return view('pages.laporan.cetak', compact(
            'tanggal',
            'peminjamanHariIni',
            'pengembalianHariIni',
            'totalPeminjamanHariIni',
            'totalPengembalianHariIni',
            'totalDendaHariIni'
        ));
    }
}