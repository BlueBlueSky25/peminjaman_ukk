@extends('layouts.app')

@section('title', 'Laporan Peminjaman Alat')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Laporan Peminjaman Alat</h2>
        <a href="{{ route('laporan.cetak', ['tanggal' => $tanggal]) }}" target="_blank" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
            <i class="fas fa-print"></i>
            <span>Cetak Laporan</span>
        </a>
    </div>

    <!-- Filter Tanggal -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('laporan.index') }}" class="flex items-center space-x-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="pt-6">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>

    <!-- Ringkasan Harian -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">
            Ringkasan Tanggal {{ date('d F Y', strtotime($tanggal)) }}
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="border-l-4 border-blue-500 pl-4">
                <p class="text-sm text-gray-600 mb-1">Total Peminjaman</p>
                <p class="text-3xl font-bold text-blue-600">{{ $totalPeminjamanHariIni }}</p>
            </div>
            <div class="border-l-4 border-green-500 pl-4">
                <p class="text-sm text-gray-600 mb-1">Total Pengembalian</p>
                <p class="text-3xl font-bold text-green-600">{{ $totalPengembalianHariIni }}</p>
            </div>
            <div class="border-l-4 border-red-500 pl-4">
                <p class="text-sm text-gray-600 mb-1">Total Denda</p>
                <p class="text-3xl font-bold text-red-600">Rp {{ number_format($totalDendaHariIni, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Data Peminjaman Hari Ini -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Data Peminjaman</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jatuh Tempo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                   @forelse($peminjamanHariIni as $item)
    <tr>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            {{ $item->created_at ? $item->created_at->format('H:i') : '-' }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            {{ $item->user->username ?? 'N/A' }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
            {{ $item->alat->nama_alat ?? 'N/A' }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            {{ $item->jumlah }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
            {{ date('d/m/Y', strtotime($item->tanggal_kembali_rencana)) }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
            {{ $item->disetujui_oleh ?? 'Administrator' }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
            Tidak ada peminjaman pada tanggal ini.
        </td>
    </tr>
@endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Data Pengembalian Hari Ini -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Data Pengembalian</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terlambat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Denda</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                   @forelse($pengembalianHariIni as $item)
    <tr>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            {{ $item->created_at ? $item->created_at->format('H:i') : '-' }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            {{ $item->peminjaman->user->username ?? 'N/A' }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
            {{ $item->peminjaman->alat->nama_alat ?? 'N/A' }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="px-2 py-1 text-xs rounded-full 
                @if($item->kondisi_alat == 'baik') bg-green-100 text-green-800
                @elseif($item->kondisi_alat == 'rusak') bg-yellow-100 text-yellow-800
                @else bg-red-100 text-red-800
                @endif">
                {{ ucfirst($item->kondisi_alat) }}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm">
            @if($item->keterlambatan_hari > 0)
                <span class="text-red-600">{{ $item->keterlambatan_hari }} hari</span>
            @else
                <span class="text-green-600">Tepat waktu</span>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            @if($item->total_denda > 0)
                <span class="text-red-600">Rp {{ number_format($item->total_denda, 0, ',', '.') }}</span>
            @else
                <span class="text-gray-600">-</span>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
            {{ session('username', 'Administrator') }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
            Tidak ada pengembalian pada tanggal ini.
        </td>
    </tr>
@endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection