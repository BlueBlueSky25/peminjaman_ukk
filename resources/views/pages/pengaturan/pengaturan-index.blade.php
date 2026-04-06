@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Pengaturan Sistem</h2>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="max-w-lg">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-1 flex items-center">
                <i class="fas fa-cog text-blue-500 mr-2"></i>
                Pengaturan Denda
            </h3>
            <p class="text-sm text-gray-500 mb-6">Tarif denda akan digunakan untuk semua perhitungan keterlambatan pengembalian.</p>

            <form action="{{ route('pengaturan.update') }}" method="POST">
                @csrf
                @method('PUT')

                @php
                    $tarifDenda = $pengaturan->where('key', 'tarif_denda')->first();
                @endphp

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $tarifDenda->label ?? 'Tarif Denda Per Hari' }}
                    </label>
                    <div class="flex items-center">
                        <span class="bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg px-3 py-2 text-gray-600 text-sm font-medium">Rp</span>
                        <input type="number" name="tarif_denda" min="0"
                            value="{{ old('tarif_denda', $tarifDenda->value ?? 5000) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-r-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    @error('tarif_denda')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-2">
                        Contoh: jika diisi <strong>5000</strong>, maka denda keterlambatan = Rp 5.000 × jumlah item × jumlah hari terlambat.
                    </p>
                </div>

                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-lg transition flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </form>
        </div>
    </div>
@endsection
