@extends('layouts.app')

@section('title', 'Daftar Alat')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Alat</h2>
            <p class="text-sm text-gray-600 mt-1">Kelola dan pantau ketersediaan alat</p>
        </div>
        @if(auth()->user()->level == 'admin')
            <button onclick="openModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition shadow-sm">
                <i class="fas fa-plus"></i>
                <span>Tambah Alat</span>
            </button>
        @endif
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 uppercase">Total Alat</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $alats->count() }}</p>
                </div>
                <i class="fas fa-boxes text-3xl text-blue-500"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 uppercase">Stok Tersedia</p>
                    <p class="text-2xl font-bold text-green-600">{{ $alats->sum('stok_tersedia') }}</p>
                </div>
                <i class="fas fa-check-circle text-3xl text-green-500"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 uppercase">Sedang Dipinjam</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $alats->sum('stok_total') - $alats->sum('stok_tersedia') }}</p>
                </div>
                <i class="fas fa-hand-holding text-3xl text-yellow-500"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 uppercase">Stok Habis</p>
                    <p class="text-2xl font-bold text-red-600">{{ $alats->where('stok_tersedia', 0)->count() }}</p>
                </div>
                <i class="fas fa-exclamation-triangle text-3xl text-red-500"></i>
            </div>
        </div>
    </div>

    <!-- Card Grid View -->
    @if($alats->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($alats as $alat)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow overflow-hidden">
                    <!-- Header Card -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 text-white">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="font-bold text-lg mb-1">{{ $alat->nama_alat }}</h3>
                                <p class="text-sm opacity-90">{{ $alat->kategori->nama_kategori ?? '-' }}</p>
                            </div>
                            <div class="ml-2">
                                <span class="px-2 py-1 text-xs rounded-full font-semibold capitalize
                                    @if($alat->kondisi == 'baik') bg-green-100 text-green-800
                                    @elseif($alat->kondisi == 'rusak') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $alat->kondisi }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Body Card -->
                    <div class="p-4">
                        <!-- Kode & Lokasi -->
                        <div class="flex items-center text-sm text-gray-600 mb-3">
                            <i class="fas fa-barcode w-5"></i>
                            <span class="ml-2">{{ $alat->kode_alat }}</span>
                            @if($alat->lokasi)
                                <i class="fas fa-map-marker-alt w-5 ml-4"></i>
                                <span class="ml-2">{{ $alat->lokasi }}</span>
                            @endif
                        </div>

                       <!-- Stok Info -->
<div class="bg-gray-50 rounded-lg p-3 mb-3">
    <div class="flex items-center justify-between mb-2">
        <span class="text-sm text-gray-600">Ketersediaan Stok</span>
        <span class="text-sm font-semibold 
            @if($alat->stok_tersedia == 0) text-red-600
            @elseif($alat->stok_tersedia < ($alat->stok_total * 0.3)) text-yellow-600
            @else text-green-600
            @endif">
            {{ $alat->stok_tersedia }} / {{ $alat->stok_total }}
        </span>
    </div>
    
    <!-- Progress Bar -->
    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-3">
        @php
            $percentage = $alat->stok_total > 0 ? ($alat->stok_tersedia / $alat->stok_total) * 100 : 0;
        @endphp
        <div class="h-2.5 rounded-full transition-all
            @if($percentage == 0) bg-red-500
            @elseif($percentage < 30) bg-yellow-500
            @else bg-green-500
            @endif" 
            style="width: {{ $percentage }}%">
        </div>
    </div>
    
    <!-- Detail Stok per Kondisi -->
    <div class="grid grid-cols-3 gap-2 text-xs">
        <div class="text-center">
            <p class="text-gray-500">Baik</p>
            <p class="font-bold text-green-600">{{ $alat->stok_tersedia }}</p>
        </div>
        <div class="text-center">
            <p class="text-gray-500">Rusak</p>
            <p class="font-bold text-yellow-600">{{ $alat->stok_rusak ?? 0 }}</p>
        </div>
        <div class="text-center">
            <p class="text-gray-500">Hilang</p>
            <p class="font-bold text-red-600">{{ $alat->stok_hilang ?? 0 }}</p>
        </div>
    </div>
</div>

                        <!-- Status Badges -->
<div class="flex items-center gap-2 mb-3">
    @if($alat->stok_tersedia == 0)
        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full font-semibold">
            <i class="fas fa-times-circle"></i> Stok Habis
        </span>
    @elseif($alat->stok_tersedia < ($alat->stok_total * 0.3))
        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full font-semibold">
            <i class="fas fa-exclamation-circle"></i> Stok Menipis
        </span>
    @else
        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-semibold">
            <i class="fas fa-check-circle"></i> Tersedia
        </span>
    @endif

    @php
        // Hitung yang sedang aktif dipinjam (status disetujui atau sebagian_kembali)
        $sedangDipinjam = \App\Models\Peminjaman::where('alat_id', $alat->alat_id)
            ->whereIn('status', ['disetujui', 'sebagian_kembali'])
            ->sum('jumlah');
    @endphp

    @if($sedangDipinjam > 0)
        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
            {{ $sedangDipinjam }} Dipinjam
        </span>
    @endif
</div>

                        <!-- Deskripsi -->
                        @if($alat->deskripsi)
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $alat->deskripsi }}</p>
                        @endif

                        <!-- Actions -->
                        @if(auth()->user()->level == 'admin')
                            <div class="flex gap-2 pt-3 border-t">
                                <button onclick="editAlat({{ $alat->alat_id }}, {{ json_encode($alat) }})" 
                                    class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium transition">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form action="{{ route('alat.destroy', $alat->alat_id) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus alat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded text-sm font-medium transition">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <i class="fas fa-boxes text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg mb-2">Belum ada data alat</p>
            <p class="text-gray-400 text-sm">Klik tombol "Tambah Alat" untuk menambahkan alat baru.</p>
        </div>
    @endif

  <!-- Modal Tambah/Edit Alat -->
<div id="alatModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-6 border w-[800px] shadow-lg rounded-md bg-white my-10">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Tambah Alat</h3>
                <p class="text-sm text-gray-500 mt-1">Isi formulir di bawah untuk menambahkan alat baru</p>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="alatForm" method="POST" action="{{ route('alat.store') }}">
            @csrf
            <input type="hidden" id="methodField" name="_method" value="POST">
            
            <!-- 2 Columns Grid -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Kolom Kiri -->
                <div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Alat <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_alat" name="nama_alat" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Contoh: Laptop Dell Latitude">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select id="kategori_id" name="kategori_id" required onchange="generateKodeAlat()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Kategori</option>
                            @foreach(\App\Models\Kategori::all() as $kat)
                                <option value="{{ $kat->kategori_id }}" data-kode="{{ strtoupper(substr($kat->nama_kategori, 0, 3)) }}">
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Kode alat akan otomatis dibuat berdasarkan kategori</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Kode Alat <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" id="kode_alat" name="kode_alat" required readonly
                                class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="AUTO">
                            <button type="button" onclick="generateKodeAlat()" class="absolute right-2 top-2 text-blue-500 hover:text-blue-700">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Format: [KATEGORI]-[NOMOR] (Contoh: ELE-001)</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Stok Total <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="stok_total" name="stok_total" min="1" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Jumlah unit">
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Kondisi <span class="text-red-500">*</span>
                        </label>
                        <select id="kondisi" name="kondisi" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Kondisi</option>
                            <option value="baik">Baik</option>
                            <option value="rusak">Rusak</option>
                            <option value="hilang">Hilang</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Lokasi Penyimpanan
                        </label>
                        <input type="text" id="lokasi" name="lokasi" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Contoh: Gudang A - Rak 3">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi
                        </label>
                        <textarea id="deskripsi" name="deskripsi" rows="5"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Spesifikasi, merek, model, atau informasi tambahan lainnya..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-3 mt-6 pt-4 border-t">
                <button type="submit" 
                    class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2.5 rounded-lg transition font-medium shadow-sm hover:shadow flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Simpan</span>
                </button>
                <button type="button" onclick="closeModal()" 
                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2.5 rounded-lg transition font-medium">
                    <i class="fas fa-times"></i>
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Auto-generate kode alat
    function generateKodeAlat() {
        const kategoriSelect = document.getElementById('kategori_id');
        const kodeInput = document.getElementById('kode_alat');
        
        if (kategoriSelect.value) {
            const selectedOption = kategoriSelect.options[kategoriSelect.selectedIndex];
            const kodeKategori = selectedOption.getAttribute('data-kode');
            
            // Generate nomor urut random (atau bisa fetch dari server untuk nomor terakhir)
            const randomNum = Math.floor(Math.random() * 900) + 100; // 100-999
            const kodeAlat = `${kodeKategori}-${randomNum}`;
            
            kodeInput.value = kodeAlat;
        } else {
            kodeInput.value = '';
        }
    }

    function openModal() {
        document.getElementById('alatModal').classList.remove('hidden');
        document.getElementById('modalTitle').textContent = 'Tambah Alat';
        document.getElementById('alatForm').action = '{{ route("alat.store") }}';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('nama_alat').value = '';
        document.getElementById('kategori_id').value = '';
        document.getElementById('kode_alat').value = '';
        document.getElementById('stok_total').value = '';
        document.getElementById('kondisi').value = '';
        document.getElementById('lokasi').value = '';
        document.getElementById('deskripsi').value = '';
        
        // Enable kode alat input untuk mode tambah
        document.getElementById('kode_alat').readOnly = false;
    }

    function closeModal() {
        document.getElementById('alatModal').classList.add('hidden');
    }

    function editAlat(id, data) {
        document.getElementById('alatModal').classList.remove('hidden');
        document.getElementById('modalTitle').textContent = 'Edit Alat';
        document.getElementById('alatForm').action = '/alat/' + id;
        document.getElementById('methodField').value = 'PUT';
        document.getElementById('nama_alat').value = data.nama_alat;
        document.getElementById('kategori_id').value = data.kategori_id;
        document.getElementById('kode_alat').value = data.kode_alat;
        document.getElementById('stok_total').value = data.stok_total;
        document.getElementById('kondisi').value = data.kondisi;
        document.getElementById('lokasi').value = data.lokasi || '';
        document.getElementById('deskripsi').value = data.deskripsi || '';
        
        // Disable kode alat input untuk mode edit
        document.getElementById('kode_alat').readOnly = true;
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('alatModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
@endsection