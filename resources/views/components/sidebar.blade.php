<aside class="w-64 bg-white min-h-screen shadow-sm p-4">
    <nav class="space-y-2">
        <!-- Dashboard - Semua Role -->
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-800' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg">
            <i class="fas fa-chart-bar {{ request()->routeIs('dashboard') ? 'text-blue-500' : 'text-gray-400' }}"></i>
            <span {{ request()->routeIs('dashboard') ? 'class=font-medium' : '' }}>Dashboard</span>
        </a>

        @php
            $userLevel = strtolower(auth()->user()->level ?? '');
        @endphp

        <!-- Alat - admin & peminjam -->
        @if(in_array($userLevel, ['admin', 'peminjam']))
            <a href="{{ route('alat.index') }}" class="flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('alat.*') ? 'bg-gray-100 text-gray-800' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg">
                <i class="fas fa-wrench {{ request()->routeIs('alat.*') ? 'text-blue-500' : 'text-gray-400' }}"></i>
                <span {{ request()->routeIs('alat.*') ? 'class=font-medium' : '' }}>Alat</span>
            </a>
        @endif

        <!-- Peminjaman - Semua Role -->
        @if(in_array($userLevel, ['admin', 'petugas', 'peminjam']))
            <a href="{{ route('peminjaman.index') }}" class="flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('peminjaman.*') ? 'bg-gray-100 text-gray-800' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg">
                <i class="fas fa-clipboard-list {{ request()->routeIs('peminjaman.*') ? 'text-blue-500' : 'text-gray-400' }}"></i>
                <span {{ request()->routeIs('peminjaman.*') ? 'class=font-medium' : '' }}>Peminjaman</span>
            </a>
        @endif

        <!-- Users - admin Only -->
        @if($userLevel === 'admin')
            <a href="{{ route('users.index') }}" class="flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('users.*') ? 'bg-gray-100 text-gray-800' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg">
                <i class="fas fa-users {{ request()->routeIs('users.*') ? 'text-blue-500' : 'text-gray-400' }}"></i>
                <span {{ request()->routeIs('users.*') ? 'class=font-medium' : '' }}>Users</span>
            </a>
        @endif

        <!-- Kategori - admin Only -->
        @if($userLevel === 'admin')
            <a href="{{ route('kategori.index') }}" class="flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('kategori.*') ? 'bg-gray-100 text-gray-800' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg">
                <i class="fas fa-folder {{ request()->routeIs('kategori.*') ? 'text-blue-500' : 'text-gray-400' }}"></i>
                <span {{ request()->routeIs('kategori.*') ? 'class=font-medium' : '' }}>Kategori</span>
            </a>
        @endif

        <!-- Pengembalian - Semua Role -->
        @if(in_array($userLevel, ['admin', 'petugas']))
            <a href="{{ route('pengembalian.index') }}" class="flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('pengembalian.*') ? 'bg-gray-100 text-gray-800' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg">
                <i class="fas fa-undo {{ request()->routeIs('pengembalian.*') ? 'text-blue-500' : 'text-gray-400' }}"></i>
                <span {{ request()->routeIs('pengembalian.*') ? 'class=font-medium' : '' }}>Pengembalian</span>
            </a>
        @endif

        <!-- Log Aktivitas - admin Only -->
        @if($userLevel === 'admin')
            <a href="{{ route('log.index') }}" class="flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('log.*') ? 'bg-gray-100 text-gray-800' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg">
                <i class="fas fa-book {{ request()->routeIs('log.*') ? 'text-blue-500' : 'text-gray-400' }}"></i>
                <span {{ request()->routeIs('log.*') ? 'class=font-medium' : '' }}>Log Aktivitas</span>
            </a>
        @endif

        <!-- Laporan - admin & petugas -->
        @if(in_array($userLevel, ['admin', 'petugas']))
            <a href="{{ route('laporan.index') }}" class="flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('laporan.*') ? 'bg-gray-100 text-gray-800' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg">
                <i class="fas fa-chart-line {{ request()->routeIs('laporan.*') ? 'text-blue-500' : 'text-gray-400' }}"></i>
                <span {{ request()->routeIs('laporan.*') ? 'class=font-medium' : '' }}>Laporan</span>
            </a>
        @endif
    </nav>
</aside>