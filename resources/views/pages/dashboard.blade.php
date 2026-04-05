@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h2>

    <!-- KPI Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Total Users - Admin Only -->
        @if($userLevel == 'admin')
            <x-kpi-card 
                title="Total Users" 
                value="{{ $totalUsers }}" 
                icon="fa-users" 
                color="blue"
            />
        @endif

        <!-- Total Alat - Admin & Petugas Only -->
        @if($userLevel == 'admin' || $userLevel == 'petugas')
            <x-kpi-card 
                title="Total Alat" 
                value="{{ $totalAlat }}" 
                icon="fa-wrench" 
                color="green"
            />
        @endif

        <!-- Peminjaman Pending -->
        <x-kpi-card 
            title="{{ $userLevel == 'admin' || $userLevel == 'petugas' ? 'Peminjaman Pending (Semua)' : 'Peminjaman Pending (Anda)' }}" 
            value="{{ $peminjamanPending }}" 
            icon="fa-hourglass-half" 
            color="yellow"
        />

        <!-- Peminjaman Aktif -->
        <x-kpi-card 
            title="{{ $userLevel == 'admin' || $userLevel == 'petugas' ? 'Peminjaman Aktif (Semua)' : 'Peminjaman Aktif (Anda)' }}" 
            value="{{ $peminjamanAktif }}" 
            icon="fa-clipboard-check" 
            color="purple"
        />

        <!-- Total Pengembalian -->
        <x-kpi-card 
            title="{{ $userLevel == 'admin' || $userLevel == 'petugas' ? 'Total Pengembalian (Semua)' : 'Total Pengembalian (Anda)' }}" 
            value="{{ $totalPengembalian }}" 
            icon="fa-check-circle" 
            color="blue"
        />

        <!-- Total Denda -->
        <x-kpi-card 
            title="{{ $userLevel == 'admin' || $userLevel == 'petugas' ? 'Total Denda (Semua)' : 'Total Denda (Anda)' }}" 
            value="Rp {{ number_format($totalDenda, 0, ',', '.') }}" 
            icon="fa-money-bill-wave" 
            color="red"
        />
    </div>

    <!-- Alat yang Masih Dipinjam -->
    @if($alatMasihDipinjam->count() > 0)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-box text-orange-500 mr-2"></i>
                {{ $userLevel == 'admin' || $userLevel == 'petugas' ? 'Alat yang Masih Dipinjam (Semua User)' : 'Alat yang Masih Anda Pinjam' }}
            </h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                @if($userLevel == 'admin' || $userLevel == 'petugas')
                                    Peminjam
                                @else
                                    Alat
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                @if($userLevel == 'admin' || $userLevel == 'petugas')
                                    Alat
                                @else
                                    Jumlah
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Pinjam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jatuh Tempo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($alatMasihDipinjam as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($userLevel == 'admin' || $userLevel == 'petugas')
                                        {{ $item->user->username ?? '-' }}
                                    @else
                                        {{ $item->alat->nama_alat ?? '-' }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    @if($userLevel == 'admin' || $userLevel == 'petugas')
                                        {{ $item->alat->nama_alat ?? '-' }}
                                    @else
                                        {{ $item->jumlah }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $item->tanggal_peminjaman->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $item->tanggal_kembali_rencana->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $today = \Carbon\Carbon::today();
                                        $jatuhTempo = $item->tanggal_kembali_rencana;
                                        $hariSisa = $today->diffInDays($jatuhTempo, false);
                                    @endphp
                                    
                                    @if($hariSisa < 0)
                                        <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-800 font-semibold">
                                            Terlambat {{ abs($hariSisa) }} hari
                                        </span>
                                    @elseif($hariSisa == 0)
                                        <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-semibold">
                                            Kembali Hari Ini
                                        </span>
                                    @elseif($hariSisa <= 3)
                                        <span class="px-3 py-1 text-xs rounded-full bg-orange-100 text-orange-800 font-semibold">
                                            {{ $hariSisa }} hari lagi
                                        </span>
                                    @else
                                        <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold">
                                            {{ $hariSisa }} hari lagi
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-check-circle text-4xl text-green-300 mb-2"></i>
                                    <p class="font-medium">Tidak ada alat yang sedang dipinjam</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Welcome Message -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-2">
            Selamat Datang! 👋
        </h3>
        <p class="text-gray-600">
            @if($userLevel == 'admin')
                Sistem Peminjaman Alat ini membantu Anda mengelola peminjaman alat dengan mudah. Gunakan menu di sidebar untuk mengakses berbagai fitur.
            @elseif($userLevel == 'petugas')
                Anda dapat menyetujui peminjaman, memantau pengembalian, dan mencetak laporan melalui menu di sidebar.
            @else
                Anda dapat melihat daftar alat yang tersedia dan mengajukan peminjaman melalui menu di sidebar. Dashboard ini menampilkan data peminjaman Anda saja.
            @endif
        </p>
    </div>
@endsection