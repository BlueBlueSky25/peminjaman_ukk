<header class="bg-white shadow-sm border-b-4 border-blue-500 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8 py-3 sm:py-3.5 flex justify-between items-center">
        <!-- Logo & Title -->
        <div class="flex items-center space-x-2 min-w-0">
            <i class="fas fa-wrench text-blue-600 text-lg sm:text-xl flex-shrink-0"></i>
            <h1 class="text-base sm:text-lg lg:text-xl font-bold text-gray-800 truncate">Sistem Peminjaman</h1>
        </div>
        
        <div class="flex items-center gap-2 sm:gap-3">
            <!-- User Card (Hidden on Small Mobile) -->
            <div class="hidden xs:flex items-center gap-2 sm:gap-3 bg-gray-50 px-2 sm:px-4 py-2 rounded-lg border border-gray-200">
                <div class="flex items-center gap-2">
                    @php
                        $level = auth()->user()->level ?? 'admin';
                        $icon = match(strtolower($level)) {
                            'admin' => 'user-shield',
                            'petugas' => 'user-cog',
                            'peminjam' => 'user',
                            default => 'user',
                        };
                        $bgColor = match(strtolower($level)) {
                            'admin' => 'bg-red-500',
                            'petugas' => 'bg-blue-500',
                            'peminjam' => 'bg-green-500',
                            default => 'bg-gray-500',
                        };
                    @endphp
                    <div class="w-7 h-7 sm:w-8 sm:h-8 {{ $bgColor }} rounded-md flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-{{ $icon }} text-white text-xs sm:text-sm"></i>
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-xs sm:text-sm font-semibold text-gray-800 leading-tight truncate max-w-[120px]">
                            {{ auth()->user()->username ?? 'user' }}
                        </p>
                        <p class="text-xs text-gray-500 leading-tight capitalize">
                            {{ $level }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu Toggle Button -->
            <button 
                onclick="toggleMobileSidebar()" 
                class="sm:hidden text-gray-600 hover:text-gray-900 transition p-2 hover:bg-gray-100 rounded-lg flex-shrink-0"
                title="Toggle Menu"
            >
                <i class="fas fa-bars text-lg"></i>
            </button>
            
            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button 
                    type="submit" 
                    class="bg-red-500 hover:bg-red-600 text-white px-3 sm:px-5 py-2 sm:py-2.5 rounded-lg font-medium transition-all duration-200 flex items-center gap-2 shadow-sm hover:shadow-md text-xs sm:text-sm flex-shrink-0"
                    title="Logout"
                >
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="hidden xs:inline">Logout</span>
                </button>
            </form>
        </div>
    </div>
</header>

<script>
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar?.classList.toggle('hidden');
    }

    // Close sidebar when clicking on a link
    document.querySelectorAll('#sidebar a').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 640) {
                document.getElementById('sidebar')?.classList.add('hidden');
            }
        });
    });
</script>