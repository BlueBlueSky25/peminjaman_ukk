@extends('layouts.app')

@section('title', 'Peminjaman')

@section('content')
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Tracking Peminjaman Alat</h2>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-600 text-sm font-medium">Menunggu Persetujuan</p>
                    <p class="text-2xl font-bold text-yellow-700">{{ $peminjamanMenunggu->count() }}</p>
                </div>
                <i class="fas fa-clock text-3xl text-yellow-300"></i>
            </div>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-600 text-sm font-medium">Sedang Dipinjam</p>
                    <p class="text-2xl font-bold text-green-700">{{ $peminjamanAktif->count() }}</p>
                </div>
                <i class="fas fa-check-circle text-3xl text-green-300"></i>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-600 text-sm font-medium">Sudah Dikembalikan</p>
                    <p class="text-2xl font-bold text-blue-700">{{ $peminjamanSelesai->count() }}</p>
                </div>
                <i class="fas fa-undo-alt text-3xl text-blue-300"></i>
            </div>
        </div>
    </div>

    <!-- Tabs -->
<div class="mb-6 border-b border-gray-200 overflow-x-auto">
    <div class="flex space-x-2 sm:space-x-4 min-w-min">
        <button onclick="showTab('menunggu')" id="btn-menunggu" class="px-3 sm:px-4 py-2 border-b-2 border-blue-500 text-blue-600 font-medium flex items-center space-x-1 sm:space-x-2 whitespace-nowrap text-sm sm:text-base">
            <i class="fas fa-hourglass-half"></i>
            <span>Menunggu</span>
            <span class="ml-1 bg-yellow-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $peminjamanMenunggu->count() }}</span>
        </button>
        <button onclick="showTab('aktif')" id="btn-aktif" class="px-3 sm:px-4 py-2 border-b-2 border-transparent text-gray-600 hover:text-gray-800 flex items-center space-x-1 sm:space-x-2 whitespace-nowrap text-sm sm:text-base">
            <i class="fas fa-backpack"></i>
            <span>Dipinjam</span>
            <span class="ml-1 bg-green-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $peminjamanAktif->count() }}</span>
        </button>
        <button onclick="showTab('selesai')" id="btn-selesai" class="px-3 sm:px-4 py-2 border-b-2 border-transparent text-gray-600 hover:text-gray-800 flex items-center space-x-1 sm:space-x-2 whitespace-nowrap text-sm sm:text-base">
            <i class="fas fa-check-double"></i>
            <span>Selesai</span>
        </button>
    </div>
</div>

    <!-- Tab: Menunggu Persetujuan -->
    <div id="tab-menunggu" class="tab-content">
        @if($peminjamanMenunggu->isEmpty())
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <i class="fas fa-check text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 text-lg">Tidak ada peminjaman yang menunggu persetujuan</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($peminjamanMenunggu as $item)
                    <div class="bg-white rounded-lg shadow p-5 border-l-4 border-yellow-500 hover:shadow-lg transition">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="font-bold text-gray-900 text-lg">{{ $item->alat->nama_alat }}</h3>
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">Menunggu</span>
                        </div>

                        <div class="space-y-2 mb-4 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>Peminjam:</span>
                                <span class="font-medium">{{ $item->user->username }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Jumlah:</span>
                                <span class="font-medium">{{ $item->jumlah }} unit</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Dari:</span>
                                <span class="font-medium">{{ $item->tanggal_peminjaman->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Hingga:</span>
                                <span class="font-medium">{{ $item->tanggal_kembali_rencana->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        @if($item->tujuan_peminjaman)
                            <div class="bg-gray-50 rounded p-2 mb-4 text-xs">
                                <p class="font-medium text-gray-700 mb-1">Tujuan:</p>
                                <p class="text-gray-600">{{ $item->tujuan_peminjaman }}</p>
                            </div>
                        @endif

                        <div class="flex space-x-2">
                            <button onclick="approvePeminjaman({{ $item->peminjaman_id }})" 
                                class="flex-1 bg-green-500 hover:bg-green-600 text-white font-medium py-2 rounded transition flex items-center justify-center space-x-1">
                                <i class="fas fa-check"></i>
                                <span>Setuju</span>
                            </button>
                            <button onclick="rejectPeminjaman({{ $item->peminjaman_id }})" 
                                class="flex-1 bg-red-500 hover:bg-red-600 text-white font-medium py-2 rounded transition flex items-center justify-center space-x-1">
                                <i class="fas fa-times"></i>
                                <span>Tolak</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Tab: Sedang Dipinjam -->
    <div id="tab-aktif" class="tab-content hidden">
        @if($peminjamanAktif->isEmpty())
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 text-lg">Tidak ada alat yang sedang dipinjam</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Peminjaman</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target Kembali</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($peminjamanAktif as $item)
                            @php
                                $today = now();
                                $targetDate = $item->tanggal_kembali_rencana;
                                $isLate = $today->gt($targetDate);
                                $daysLeft = $today->diffInDays($targetDate, false);
                            @endphp
                            <tr class="@if($isLate) bg-red-50 @elseif($daysLeft <= 1) bg-yellow-50 @endif hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->alat->nama_alat }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $item->user->username }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $item->jumlah }} unit</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $item->tanggal_peminjaman->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="font-medium">{{ $item->tanggal_kembali_rencana->format('d/m/Y') }}</div>
                                    @if($isLate)
                                        <p class="text-xs text-red-600"><i class="fas fa-exclamation-triangle"></i> Terlambat {{ abs($daysLeft) }} hari</p>
                                    @elseif($daysLeft <= 1)
                                        <p class="text-xs text-yellow-600"><i class="fas fa-clock"></i> Harus segera dikembalikan</p>
                                    @else
                                        <p class="text-xs text-gray-500">Tinggal {{ $daysLeft }} hari</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Disetujui
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('pengembalian.index') }}" 
                                        class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded text-xs transition">
                                        <i class="fas fa-undo"></i> Catat Pengembalian
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div><!-- Tab: Sedang Dipinjam -->
<div id="tab-aktif" class="tab-content hidden">
    @if($peminjamanAktif->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500 text-lg">Tidak ada alat yang sedang dipinjam</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- TAMBAH: overflow-x-auto wrapper -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Alat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Peminjam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Tgl Peminjaman</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Target Kembali</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($peminjamanAktif as $item)
                            @php
                                $today = now();
                                $targetDate = $item->tanggal_kembali_rencana;
                                $isLate = $today->gt($targetDate);
                                $daysLeft = $today->diffInDays($targetDate, false);
                            @endphp
                            <tr class="@if($isLate) bg-red-50 @elseif($daysLeft <= 1) bg-yellow-50 @endif hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->alat->nama_alat }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $item->user->username }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $item->jumlah }} unit</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $item->tanggal_peminjaman->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="font-medium">{{ $item->tanggal_kembali_rencana->format('d/m/Y') }}</div>
                                    @if($isLate)
                                        <p class="text-xs text-red-600"><i class="fas fa-exclamation-triangle"></i> Terlambat {{ abs($daysLeft) }} hari</p>
                                    @elseif($daysLeft <= 1)
                                        <p class="text-xs text-yellow-600"><i class="fas fa-clock"></i> Harus segera dikembalikan</p>
                                    @else
                                        <p class="text-xs text-gray-500">Tinggal {{ $daysLeft }} hari</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 whitespace-nowrap">
                                        Disetujui
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('pengembalian.index') }}" 
                                        class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded text-xs transition whitespace-nowrap inline-block">
                                        <i class="fas fa-undo"></i> Catat Pengembalian
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

   <!-- Tab: Selesai -->
<div id="tab-selesai" class="tab-content hidden">
    @if($peminjamanSelesai->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <i class="fas fa-check-circle text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500 text-lg">Belum ada peminjaman yang selesai</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- ✅ TAMBAH: overflow-x-auto wrapper -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Alat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Peminjam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Tgl Peminjaman</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Tgl Kembali</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($peminjamanSelesai as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->alat->nama_alat }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $item->user->username }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $item->tanggal_peminjaman->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $item->pengembalian->last()->tanggal_kembali_aktual->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 whitespace-nowrap">
                                        Dikembalikan
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

    <script>
        function showTab(tab) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('button[id^="btn-"]').forEach(el => {
                el.classList.remove('border-blue-500', 'text-blue-600');
                el.classList.add('border-transparent', 'text-gray-600');
            });

            document.getElementById('tab-' + tab).classList.remove('hidden');
            document.getElementById('btn-' + tab).classList.remove('border-transparent', 'text-gray-600');
            document.getElementById('btn-' + tab).classList.add('border-blue-500', 'text-blue-600');
        }

        function approvePeminjaman(id) {
            if (confirm('Setuju dengan peminjaman ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/peminjaman/' + id + '/approve';
                
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                
                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'PATCH';
                
                form.appendChild(csrf);
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function rejectPeminjaman(id) {
            if (confirm('Tolak peminjaman ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/peminjaman/' + id;
                
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                
                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'PUT';
                
                const status = document.createElement('input');
                status.type = 'hidden';
                status.name = 'status';
                status.value = 'ditolak';
                
                form.appendChild(csrf);
                form.appendChild(method);
                form.appendChild(status);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection