@extends('layouts.app')

@section('title', 'Peminjaman')

@section('content')
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Peminjaman Alat</h2>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left: Form Peminjaman -->
        <div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-plus-circle text-blue-500 mr-2"></i>
                    Ajukan Peminjaman
                </h3>

                <form action="{{ route('peminjaman.store') }}" method="POST" id="peminjamanForm">
                    @csrf

                    <!-- Hidden user_id (auto dari login) -->
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alat <span class="text-red-600">*</span></label>
                        <select name="alat_id" id="alat_select" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Alat</option>
                            @foreach($alats as $alat)
                                <option value="{{ $alat->alat_id }}" data-max="{{ $alat->stok_tersedia }}" data-nama="{{ $alat->nama_alat }}">
                                    {{ $alat->nama_alat }} (Tersedia: {{ $alat->stok_tersedia }})
                                </option>
                            @endforeach
                        </select>
                        @error('alat_id')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah <span class="text-red-600">*</span></label>
                        <input type="number" id="jumlah_input" name="jumlah" min="1" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Jumlah yang dipinjam">
                        <p id="stok_info" class="text-xs text-gray-500 mt-1"></p>
                        @error('jumlah')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Peminjaman <span class="text-red-600">*</span></label>
                        <input type="date" name="tanggal_peminjaman" required min="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('tanggal_peminjaman')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kembali Rencana <span class="text-red-600">*</span></label>
                        <input type="date" name="tanggal_kembali_rencana" id="tanggal_kembali" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('tanggal_kembali_rencana')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tujuan Peminjaman</label>
                        <textarea name="tujuan_peminjaman" rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Untuk keperluan..."></textarea>
                    </div>

                    <button type="submit" 
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 rounded-lg transition flex items-center justify-center space-x-2">
                        <i class="fas fa-send"></i>
                        <span>Ajukan Peminjaman</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: History Peminjaman -->
        <div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-history text-purple-500 mr-2"></i>
                    Riwayat Peminjaman
                </h3>

                @if($peminjaman->isEmpty())
                    <div class="text-center py-8">
                        <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Belum ada riwayat peminjaman</p>
                    </div>
                @else
                    <div class="space-y-3 max-h-[600px] overflow-y-auto">
                        @foreach($peminjaman as $item)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $item->alat->nama_alat }}</h4>
                                        <p class="text-xs text-gray-500">{{ $item->tanggal_peminjaman->format('d/m/Y') }} - {{ $item->tanggal_kembali_rencana->format('d/m/Y') }}</p>
                                    </div>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($item->status == 'disetujui') bg-green-100 text-green-800
                                        @elseif($item->status == 'menunggu') bg-yellow-100 text-yellow-800
                                        @elseif($item->status == 'ditolak') bg-red-100 text-red-800
                                        @elseif($item->status == 'dikembalikan') bg-blue-100 text-blue-800
                                        @endif">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-sm text-gray-600 mb-3">
                                    <div>
                                        <p class="text-xs text-gray-500">Jumlah</p>
                                        <p class="font-medium">{{ $item->jumlah }} unit</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Disetujui oleh</p>
                                        <p class="font-medium">{{ $item->petugas->username ?? '-' }}</p>
                                    </div>
                                </div>

                                @if($item->tujuan_peminjaman)
                                    <p class="text-xs text-gray-600 mb-2">
                                        <span class="font-medium">Tujuan:</span> {{ $item->tujuan_peminjaman }}
                                    </p>
                                @endif

                                @if($item->status == 'menunggu')
                                    <p class="text-xs text-blue-600 flex items-center">
                                        <i class="fas fa-clock mr-1"></i>
                                        Menunggu persetujuan petugas
                                    </p>
                                @elseif($item->status == 'ditolak')
                                    <p class="text-xs text-red-600 flex items-center">
                                        <i class="fas fa-times mr-1"></i>
                                        Peminjaman ditolak
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.getElementById('alat_select').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const maxStok = selected.getAttribute('data-max');
            const jumlahInput = document.getElementById('jumlah_input');
            const stokInfo = document.getElementById('stok_info');
            
            if (maxStok) {
                jumlahInput.max = maxStok;
                stokInfo.textContent = `Maksimal: ${maxStok} unit`;
            } else {
                jumlahInput.max = '';
                stokInfo.textContent = '';
            }

            const today = new Date().toISOString().split('T')[0];
            const tglPinjam = document.querySelector('input[name="tanggal_peminjaman"]');
            const tglKembali = document.getElementById('tanggal_kembali');

            // Set min hari ini
            tglPinjam.min = today;

            // Saat tanggal peminjaman berubah, update min tanggal kembali
            tglPinjam.addEventListener('change', function() {
                tglKembali.min = this.value;
                if (tglKembali.value && tglKembali.value <= this.value) {
                    tglKembali.value = '';
                }
            });


        });
    </script>
@endsection