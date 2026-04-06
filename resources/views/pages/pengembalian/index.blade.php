@extends('layouts.app')

@section('title', 'Pengembalian Alat')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Pengembalian & Denda</h2>
            <p class="text-sm text-gray-600 mt-1">Kelola proses pengembalian alat dan verifikasi pembayaran denda</p>
        </div>
        @if(auth()->user()->level == 'admin' || auth()->user()->level == 'petugas')
            <button onclick="openModal()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition shadow-sm">
                <i class="fas fa-undo"></i>
                <span>Proses Pengembalian</span>
            </button>
        @endif
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-xs text-gray-600 uppercase">Total Pengembalian</p>
            <p class="text-3xl font-bold text-blue-600">{{ $pengembalian->count() }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <p class="text-xs text-gray-600 uppercase">Denda Belum Lunas</p>
            <p class="text-3xl font-bold text-red-600">{{ $dendaBelumLunas->count() }}</p>
            <p class="text-xs text-red-500 mt-1">Rp {{ number_format($dendaBelumLunas->sum('total_denda'), 0, ',', '.') }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <p class="text-xs text-gray-600 uppercase">Barang Masih Dipinjam</p>
            <p class="text-3xl font-bold text-yellow-600">{{ $barangMasihDipinjam->count() }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-xs text-gray-600 uppercase">Denda Lunas</p>
            <p class="text-3xl font-bold text-green-600">{{ $pengembalian->where('status_denda', 'lunas')->count() }}</p>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex gap-4 mb-6 border-b border-gray-200">
        <button class="tab-btn active px-4 py-3 font-medium text-blue-600 border-b-2 border-blue-600" data-tab="denda-belum-lunas">
            <i class="fas fa-money-bill-wave mr-2"></i>Denda Belum Dibayar
        </button>
        <button class="tab-btn px-4 py-3 font-medium text-gray-600" data-tab="barang-dipinjam">
            <i class="fas fa-box mr-2"></i>Barang Masih Dipinjam
        </button>
        <button class="tab-btn px-4 py-3 font-medium text-gray-600" data-tab="riwayat-pengembalian">
            <i class="fas fa-history mr-2"></i>Riwayat Pengembalian
        </button>
    </div>

    <!-- TAB 1: Denda Belum Lunas -->
    <div id="denda-belum-lunas" class="tab-content">
        @if($dendaBelumLunas->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-red-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Peminjam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Alat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Tgl Kembali</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Terlambat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Total Denda</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($dendaBelumLunas as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $item->peminjaman->nama_peminjam ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $item->peminjaman->alat->nama_alat }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $item->tanggal_kembali_aktual->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($item->keterlambatan_hari > 0)
                                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full font-medium">
                                            {{ $item->keterlambatan_hari }} hari
                                        </span>
                                    @else
                                        <span class="text-green-600">Tepat waktu</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-600">
                                    Rp {{ number_format($item->total_denda, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full font-semibold">
                                        Belum Lunas
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="openVerifikasiModal({{ $item->pengembalian_id }}, {{ $item->total_denda }})" 
                                        class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded text-xs font-medium transition">
                                        <i class="fas fa-check mr-1"></i>Bayar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-check-circle text-6xl text-green-300 mb-4"></i>
                <p class="text-gray-500 text-lg">Semua denda sudah dilunasi!</p>
            </div>
        @endif
    </div>

    <!-- TAB 2: Barang Masih Dipinjam -->
    <div id="barang-dipinjam" class="tab-content hidden">
        @if($barangMasihDipinjam->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-yellow-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Peminjam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Alat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Tgl Pinjam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Jatuh Tempo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Denda Estimasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                       @foreach($barangMasihDipinjam as $item)
                            @php
                                $today = \Carbon\Carbon::today();
                                $jatuhTempo = \Carbon\Carbon::parse($item->tanggal_kembali_rencana);
                                $hariTerlambat = $today->diffInDays($jatuhTempo, false);
                                $tarifDenda = \App\Models\Pengaturan::where('key', 'tarif_denda')->first()?->value ?? 5000;
                                $dendaEstimasi = max(0, abs($hariTerlambat) * $tarifDenda * $item->jumlah);
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $item->peminjaman->nama_peminjam ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $item->alat->nama_alat }} ({{ $item->jumlah }}x)
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $item->tanggal_peminjaman->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $jatuhTempo->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($hariTerlambat < 0)
                                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                            Terlambat {{ abs($hariTerlambat) }} hari
                                        </span>
                                    @elseif($hariTerlambat == 0)
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">
                                            Kembali hari ini
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                            {{ $hariTerlambat }} hari lagi
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-orange-600">
                                    Rp {{ number_format($dendaEstimasi, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded font-medium">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-check-circle text-6xl text-green-300 mb-4"></i>
                <p class="text-gray-500 text-lg">Semua barang sudah dikembalikan!</p>
            </div>
        @endif
    </div>

    <!-- TAB 3: Riwayat Pengembalian -->
    <div id="riwayat-pengembalian" class="tab-content hidden">
        @if($pengembalian->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Peminjam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Alat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Tgl Kembali</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Kondisi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Denda</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pengembalian as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->peminjaman->nama_peminjam ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $item->peminjaman->alat->nama_alat }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $item->tanggal_kembali_aktual->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full font-semibold capitalize
                                        @if($item->kondisi_alat == 'baik') bg-green-100 text-green-800
                                        @elseif($item->kondisi_alat == 'rusak') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ $item->kondisi_alat }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($item->total_denda > 0)
                                        <span class="text-red-600">Rp {{ number_format($item->total_denda, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-gray-600">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full font-semibold
                                        @if($item->status_denda == 'lunas') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ str_replace('_', ' ', ucfirst($item->status_denda)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($item->status_denda == 'belum_lunas')
                                        <button onclick="openVerifikasiModal({{ $item->pengembalian_id }}, {{ $item->total_denda }})" 
                                            class="px-2 py-1 bg-green-500 hover:bg-green-600 text-white rounded text-xs font-medium transition">
                                            Bayar
                                        </button>
                                    @else
                                        <span class="text-gray-500 text-xs">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">Belum ada data pengembalian</p>
            </div>
        @endif
    </div>

    <!-- Modal Proses Pengembalian -->
<div id="pengembalianModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Proses Pengembalian</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('pengembalian.store') }}" method="POST" id="peminjamanForm">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Peminjaman</label>
                <select name="peminjaman_id" id="peminjaman_select" required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Pilih Peminjaman</option>
                   @foreach($peminjamanBelumSelesai as $pinjam)
                    <option value="{{ $pinjam->peminjaman_id }}" 
                        data-jatuh-tempo="{{ $pinjam->tanggal_kembali_rencana->format('Y-m-d') }}"
                        data-user="{{ $pinjam->nama_peminjam ?? '-' }}"
                        data-alat="{{ $pinjam->alat->nama_alat }}"
                        data-jumlah="{{ $pinjam->jumlah }}">
                        {{ $pinjam->nama_peminjam ?? '-' }} - {{ $pinjam->alat->nama_alat }} ({{ $pinjam->jumlah }}x)
                    </option>
                    @endforeach
                </select>
                <p id="info_peminjaman" class="text-xs text-gray-500 mt-1"></p>
            </div>

            <!-- Tanggal Kembali -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kembali</label>
                <input type="date" id="tanggal_kembali" name="tanggal_kembali_aktual" required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    value="{{ date('Y-m-d') }}">
                <p id="info_keterlambatan" class="text-xs mt-1"></p>
            </div>

            <!-- Breakdown Kondisi per Item -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">Kondisi Pengembalian</label>
                <div class="space-y-2">
                    <div>
                        <label class="text-xs text-gray-600">
                            <i class="fas fa-check text-green-600 mr-1"></i>Baik
                        </label>
                        <input type="number" name="kondisi_baik" id="kondisi_baik" min="0" value="0" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mr-1"></i>Rusak
                        </label>
                        <input type="number" name="kondisi_rusak" id="kondisi_rusak" min="0" value="0" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">
                            <i class="fas fa-times text-red-600 mr-1"></i>Hilang
                        </label>
                        <input type="number" name="kondisi_hilang" id="kondisi_hilang" min="0" value="0" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>
                <p id="total_kembali" class="text-xs text-blue-600 mt-2 font-semibold"></p>
                <!-- ✅ TAMBAH: Error message jika validation gagal -->
                @error('kondisi_baik')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                <textarea name="keterangan" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Catatan tambahan (opsional)"></textarea>
            </div>

            <div class="flex space-x-2">
                <button type="submit" id="submitBtn"
                    class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Proses
                </button>
                <button type="button" onclick="closeModal()" 
                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 rounded-lg transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>


    <!-- Modal Verifikasi Pembayaran -->
    <div id="verifikasiModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-6 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Verifikasi Pembayaran Denda</h3>
                <button onclick="closeVerifikasiModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="verifikasiForm" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Denda</label>
                    <input type="text" id="total_denda_display" readonly 
                        class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 font-bold">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="jumlah_bayar" id="jumlah_bayar" step="1000" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Masukkan jumlah yang diterima">
                    <p id="keterangan_bayar" class="text-xs text-gray-500 mt-1"></p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_bayar_denda" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        value="{{ date('Y-m-d') }}">
                </div>

                <div class="mb-4">
                    <select name="metode_pembayaran" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="cash" selected>Cash</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan_pembayaran" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Catatan tambahan (opsional)"></textarea>
                </div>

                <div class="flex space-x-2">
                    <button type="submit" 
                        class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg transition font-medium">
                        <i class="fas fa-check mr-1"></i>Verifikasi
                    </button>
                    <button type="button" onclick="closeVerifikasiModal()" 
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 rounded-lg transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

   <script>
    // Modal Proses Pengembalian
    function openModal() {
        document.getElementById('pengembalianModal').classList.remove('hidden');
        resetForm();
    }

    function closeModal() {
        document.getElementById('pengembalianModal').classList.add('hidden');
    }

    function resetForm() {
        document.getElementById('peminjaman_select').value = '';
        document.getElementById('tanggal_kembali').value = '{{ date("Y-m-d") }}';
        document.getElementById('kondisi_baik').value = 0;
        document.getElementById('kondisi_rusak').value = 0;
        document.getElementById('kondisi_hilang').value = 0;
        document.getElementById('total_kembali').textContent = '';
        document.getElementById('info_peminjaman').textContent = '';
        document.getElementById('info_keterlambatan').textContent = '';
    }

    const peminjamanSelect = document.getElementById('peminjaman_select');
    const kondisiBaik = document.getElementById('kondisi_baik');
    const kondisiRusak = document.getElementById('kondisi_rusak');
    const kondisiHilang = document.getElementById('kondisi_hilang');
    const totalKembaliText = document.getElementById('total_kembali');
    const infoPeminjaman = document.getElementById('info_peminjaman');
    const submitBtn = document.getElementById('submitBtn');

    function updateKondisiInfo() {
        const selected = peminjamanSelect.options[peminjamanSelect.selectedIndex];
        const jumlahPinjam = parseInt(selected.getAttribute('data-jumlah')) || 0;
        
        if (!selected.getAttribute('data-user')) return; // Skip jika placeholder
        
        // Display peminjaman info
        const user = selected.getAttribute('data-user');
        const alat = selected.getAttribute('data-alat');
        infoPeminjaman.textContent = `${user} - ${alat} (${jumlahPinjam}x)`;
        
        // Calculate total kembali
        const baik = parseInt(kondisiBaik.value) || 0;
        const rusak = parseInt(kondisiRusak.value) || 0;
        const hilang = parseInt(kondisiHilang.value) || 0;
        const total = baik + rusak + hilang;

        // VALIDASI & ENABLE/DISABLE SUBMIT
        let isValid = true;
        if (total > jumlahPinjam && jumlahPinjam > 0) {
            totalKembaliText.innerHTML = `<span class="text-red-600 font-semibold">❌ Total (${total}) melebihi jumlah pinjam (${jumlahPinjam})</span>`;
            isValid = false;
        } else if (total < jumlahPinjam && total > 0) {
            totalKembaliText.innerHTML = `<span class="text-orange-600 font-semibold">⚠ Hanya ${total}/${jumlahPinjam} item dikembalikan</span>`;
            isValid = true;
        } else if (total === jumlahPinjam && total > 0) {
            totalKembaliText.innerHTML = `<span class="text-green-600 font-semibold">✓ Semua ${total} item dikembalikan</span>`;
            isValid = true;
        } else if (total === 0) {
            totalKembaliText.innerHTML = `<span class="text-gray-500">Masukkan jumlah yang dikembalikan</span>`;
            isValid = false;
        }

        // Enable/disable submit button
        submitBtn.disabled = !isValid || jumlahPinjam === 0;
    }

    peminjamanSelect.addEventListener('change', updateKondisiInfo);
    kondisiBaik.addEventListener('input', updateKondisiInfo);
    kondisiRusak.addEventListener('input', updateKondisiInfo);
    kondisiHilang.addEventListener('input', updateKondisiInfo);

    // ✅ FIX: Prevent modal click from blocking tab buttons
    document.getElementById('pengembalianModal').addEventListener('click', function(event) {
        if (event.target === this || event.target.id === 'pengembalianModal') {
            closeModal();
        }
    });

    // ✅ FIX: Stop propagation saat klik di form
    document.querySelector('#pengembalianModal form')?.addEventListener('click', function(event) {
        event.stopPropagation();
    });

    // ✅ TAB SWITCHING - Ini yang hilang!
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            
            // Show selected tab
            document.getElementById(tabName).classList.remove('hidden');
            
            // Update button styles
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                b.classList.add('text-gray-600');
            });
            
            this.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
            this.classList.remove('text-gray-600');
        });
    });

    // ✅ MODAL VERIFIKASI PEMBAYARAN
    function openVerifikasiModal(pengembalianId, totalDenda) {
        const form = document.getElementById('verifikasiForm');
        form.action = `/pengembalian/${pengembalianId}/verifikasi`;
        document.getElementById('total_denda_display').value = `Rp ${parseInt(totalDenda).toLocaleString('id-ID')}`;
        document.getElementById('jumlah_bayar').value = totalDenda;
        document.getElementById('jumlah_bayar').max = totalDenda;
        document.getElementById('verifikasiModal').classList.remove('hidden');
        
        updateKeteranganBayar(totalDenda);
    }

    function closeVerifikasiModal() {
        document.getElementById('verifikasiModal').classList.add('hidden');
    }

    function updateKeteranganBayar(totalDenda) {
        const jumlahInput = document.getElementById('jumlah_bayar');
        const keterangan = document.getElementById('keterangan_bayar');
        
        // Remove old listener dengan clone
        const newInput = jumlahInput.cloneNode(true);
        jumlahInput.parentNode.replaceChild(newInput, jumlahInput);
        
        newInput.addEventListener('input', function() {
            const jumlah = parseInt(this.value) || 0;
            if (jumlah < totalDenda) {
                keterangan.textContent = `⚠ Kurang: Rp ${(totalDenda - jumlah).toLocaleString('id-ID')}`;
                keterangan.className = 'text-xs text-orange-600 mt-1';
            } else if (jumlah > totalDenda) {
                keterangan.textContent = `✓ Kembali: Rp ${(jumlah - totalDenda).toLocaleString('id-ID')}`;
                keterangan.className = 'text-xs text-green-600 mt-1';
            } else {
                keterangan.textContent = '✓ Pas';
                keterangan.className = 'text-xs text-green-600 mt-1';
            }
        });
    }

    // ✅ VERIFIKASI MODAL CLICK
    document.getElementById('verifikasiModal')?.addEventListener('click', function(event) {
        if (event.target === this || event.target.id === 'verifikasiModal') {
            closeVerifikasiModal();
        }
    });

    document.querySelector('#verifikasiModal form')?.addEventListener('click', function(event) {
        event.stopPropagation();
    });
</script>
@endsection