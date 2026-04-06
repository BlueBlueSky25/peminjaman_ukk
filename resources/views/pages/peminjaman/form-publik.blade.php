<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Peminjaman Alat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-10 px-4">

    <div class="w-full max-w-lg">

        <!-- Header -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-600 rounded-full mb-3">
                <i class="fas fa-tools text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Peminjaman Alat</h1>
            <p class="text-gray-500 text-sm mt-1">Isi form di bawah untuk mengajukan peminjaman</p>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-4 rounded-lg mb-6">
                <div class="flex items-start space-x-3">
                    <i class="fas fa-check-circle text-green-500 mt-0.5 text-lg"></i>
                    <div>
                        <p class="font-semibold mb-1">Peminjaman Berhasil Diajukan!</p>
                        <p class="text-sm">{!! session('success') !!}</p>
                        <p class="text-sm mt-1">Struk telah dikirim ke email kamu. Cek inbox atau folder spam.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Error -->
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <form action="{{ route('peminjaman.storePublic') }}" method="POST">
                @csrf

                <!-- Nama Peminjam -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_peminjam" value="{{ old('nama_peminjam') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                        placeholder="Masukkan nama lengkap">
                    @error('nama_peminjam')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                        placeholder="contoh@email.com">
                    <p class="text-xs text-gray-400 mt-1">Struk & kode peminjaman akan dikirim ke email ini</p>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pilih Alat -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Alat <span class="text-red-500">*</span>
                    </label>
                    <select name="alat_id" id="alat_select" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="">-- Pilih Alat --</option>
                        @foreach($alats as $alat)
                            <option value="{{ $alat->alat_id }}"
                                data-max="{{ $alat->stok_tersedia }}"
                                {{ old('alat_id') == $alat->alat_id ? 'selected' : '' }}>
                                {{ $alat->nama_alat }} (Tersedia: {{ $alat->stok_tersedia }} unit)
                            </option>
                        @endforeach
                    </select>
                    @error('alat_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jumlah -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Jumlah <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="jumlah" id="jumlah_input" min="1" required
                        value="{{ old('jumlah') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                        placeholder="Jumlah yang dipinjam">
                    <p id="stok_info" class="text-xs text-gray-400 mt-1"></p>
                    @error('jumlah')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Peminjaman -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tanggal Peminjaman <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_peminjaman" id="tgl_pinjam" required
                        min="{{ date('Y-m-d') }}" value="{{ old('tanggal_peminjaman', date('Y-m-d')) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    @error('tanggal_peminjaman')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Kembali -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Rencana Tanggal Kembali <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_kembali_rencana" id="tgl_kembali" required
                        value="{{ old('tanggal_kembali_rencana') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    @error('tanggal_kembali_rencana')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tujuan -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Peminjaman</label>
                    <textarea name="tujuan_peminjaman" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                        placeholder="Untuk keperluan...">{{ old('tujuan_peminjaman') }}</textarea>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition flex items-center justify-center space-x-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>Ajukan Peminjaman</span>
                </button>
            </form>
        </div>

        <!-- Login link untuk petugas -->
        <p class="text-center text-xs text-gray-400 mt-4">
            Petugas? <a href="{{ route('login') }}" class="text-blue-500 hover:underline">Login di sini</a>
        </p>

    </div>

    <script>
        // Update max jumlah sesuai stok alat yang dipilih
        document.getElementById('alat_select').addEventListener('change', function () {
            const max = this.options[this.selectedIndex].getAttribute('data-max');
            const jumlahInput = document.getElementById('jumlah_input');
            const stokInfo = document.getElementById('stok_info');
            if (max) {
                jumlahInput.max = max;
                stokInfo.textContent = 'Maksimal: ' + max + ' unit';
            } else {
                jumlahInput.max = '';
                stokInfo.textContent = '';
            }
        });

        // Update min tanggal kembali saat tanggal pinjam berubah
        document.getElementById('tgl_pinjam').addEventListener('change', function () {
            const tglKembali = document.getElementById('tgl_kembali');
            tglKembali.min = this.value;
            if (tglKembali.value && tglKembali.value <= this.value) {
                tglKembali.value = '';
            }
        });

        // Set min tanggal kembali saat load
        const tglPinjam = document.getElementById('tgl_pinjam');
        const tglKembali = document.getElementById('tgl_kembali');
        if (tglPinjam.value) {
            tglKembali.min = tglPinjam.value;
        }
    </script>

</body>
</html>
