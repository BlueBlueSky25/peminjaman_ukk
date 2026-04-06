<aside id="sidebar" class="hidden sm:block fixed sm:static top-16 left-0 right-0 sm:w-64 bg-white min-h-screen shadow-sm p-4 z-30 max-h-[calc(100vh-64px)] overflow-y-auto">
    <nav class="space-y-1 sm:space-y-2">
        <!-- Dashboard - Semua Role -->
        <a href="{{ route('dashboard') }}" 
            class="flex items-center space-x-3 px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg transition">
            <i class="fas fa-chart-bar {{ request()->routeIs('dashboard') ? 'text-blue-500' : 'text-gray-400' }} w-4 sm:w-5 flex-shrink-0"></i>
            <span class="truncate">Dashboard</span>
        </a>

        @php
            $userLevel = strtolower(auth()->user()->level ?? '');
        @endphp

        <!-- Alat - admin & peminjam -->
        @if(in_array($userLevel, ['admin', 'peminjam']))
            <a href="{{ route('alat.index') }}" 
                class="flex items-center space-x-3 px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base {{ request()->routeIs('alat.*') ? 'bg-blue-100 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg transition">
                <i class="fas fa-wrench {{ request()->routeIs('alat.*') ? 'text-blue-500' : 'text-gray-400' }} w-4 sm:w-5 flex-shrink-0"></i>
                <span class="truncate">Alat</span>
            </a>
        @endif

        <!-- Peminjaman - Semua Role -->
        @if(in_array($userLevel, ['admin', 'petugas', 'peminjam']))
            <a href="{{ route('peminjaman.index') }}" 
                class="flex items-center space-x-3 px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base {{ request()->routeIs('peminjaman.*') ? 'bg-blue-100 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg transition">
                <i class="fas fa-clipboard-list {{ request()->routeIs('peminjaman.*') ? 'text-blue-500' : 'text-gray-400' }} w-4 sm:w-5 flex-shrink-0"></i>
                <span class="truncate">Peminjaman</span>
            </a>
        @endif

        <!-- Pengembalian - admin & petugas -->
        @if(in_array($userLevel, ['admin', 'petugas']))
            <a href="{{ route('pengembalian.index') }}" 
                class="flex items-center space-x-3 px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base {{ request()->routeIs('pengembalian.*') ? 'bg-blue-100 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg transition">
                <i class="fas fa-undo {{ request()->routeIs('pengembalian.*') ? 'text-blue-500' : 'text-gray-400' }} w-4 sm:w-5 flex-shrink-0"></i>
                <span class="truncate">Pengembalian</span>
            </a>
        @endif

        <!-- Divider untuk Admin -->
        @if($userLevel === 'admin')
            <div class="border-t border-gray-200 my-2 sm:my-4"></div>
            <p class="px-3 sm:px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Admin</p>

            <!-- Users -->
            <a href="{{ route('users.index') }}" 
                class="flex items-center space-x-3 px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base {{ request()->routeIs('users.*') ? 'bg-blue-100 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg transition">
                <i class="fas fa-users {{ request()->routeIs('users.*') ? 'text-blue-500' : 'text-gray-400' }} w-4 sm:w-5 flex-shrink-0"></i>
                <span class="truncate">Users</span>
            </a>

            <!-- Kategori -->
            <a href="{{ route('kategori.index') }}" 
                class="flex items-center space-x-3 px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base {{ request()->routeIs('kategori.*') ? 'bg-blue-100 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg transition">
                <i class="fas fa-folder {{ request()->routeIs('kategori.*') ? 'text-blue-500' : 'text-gray-400' }} w-4 sm:w-5 flex-shrink-0"></i>
                <span class="truncate">Kategori</span>
            </a>

            <!-- Log Aktivitas -->
            <a href="{{ route('log.index') }}" 
                class="flex items-center space-x-3 px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base {{ request()->routeIs('log.*') ? 'bg-blue-100 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg transition">
                <i class="fas fa-book {{ request()->routeIs('log.*') ? 'text-blue-500' : 'text-gray-400' }} w-4 sm:w-5 flex-shrink-0"></i>
                <span class="truncate">Log Aktivitas</span>
            </a>

            <!-- Pengaturan Denda -->

            <!--<a href="{{ route('pengaturan.index-pengaturan') }}" 
                class="flex items-center space-x-3 px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base {{ request()->routeIs('pengaturan.*') ? 'bg-blue-100 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg transition">
                <i class="fas fa-book {{ request()->routeIs('pengaturan.*') ? 'text-blue-500' : 'text-gray-400' }} w-4 sm:w-5 flex-shrink-0"></i>
                <span class="truncate">Pengaturan Denda</span>
            </a> -->

            <!-- Laporan -->
            <a href="{{ route('laporan.index') }}" 
                class="flex items-center space-x-3 px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base {{ request()->routeIs('laporan.*') ? 'bg-blue-100 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg transition">
                <i class="fas fa-chart-line {{ request()->routeIs('laporan.*') ? 'text-blue-500' : 'text-gray-400' }} w-4 sm:w-5 flex-shrink-0"></i>
                <span class="truncate">Laporan</span>
            </a>
        @endif

        <!-- Laporan untuk Petugas -->
        @if($userLevel === 'petugas')
            <div class="border-t border-gray-200 my-2 sm:my-4"></div>
            <a href="{{ route('laporan.index') }}" 
                class="flex items-center space-x-3 px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base {{ request()->routeIs('laporan.*') ? 'bg-blue-100 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg transition">
                <i class="fas fa-chart-line {{ request()->routeIs('laporan.*') ? 'text-blue-500' : 'text-gray-400' }} w-4 sm:w-5 flex-shrink-0"></i>
                <span class="truncate">Laporan</span>
            </a>
        @endif
    </nav>

    <!-- Close Button (Mobile Only) -->
    <div class="sm:hidden p-4 border-t border-gray-200 mt-4">
        <button 
            onclick="toggleMobileSidebar()" 
            class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition text-sm"
        >
            <i class="fas fa-times"></i>
            <span>Tutup Menu</span>
        </button>
    </div>
</aside>

<!-- Overlay untuk Mobile -->
<div 
    id="sidebar-overlay" 
    class="hidden sm:hidden fixed inset-0 bg-black bg-opacity-50 z-20 top-16"
    onclick="toggleMobileSidebar()"
></div>

<script>
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        
        sidebar?.classList.toggle('hidden');
        overlay?.classList.toggle('hidden');
    }
</script>