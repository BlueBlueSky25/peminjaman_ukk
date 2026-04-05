<header class="bg-white shadow-sm border-b-4 border-red-500 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8 py-2.5 sm:py-3.5 flex justify-between items-center gap-2">
        <!-- Logo & Title -->
        <div class="flex items-center space-x-2 min-w-0 flex-1">
            <i class="fas fa-wrench text-blue-600 text-lg sm:text-xl flex-shrink-0"></i>
            <h1 class="text-sm sm:text-base lg:text-lg font-bold text-gray-800 truncate">Sistem Peminjaman</h1>
        </div>
        
        <div class="flex items-center gap-1 sm:gap-3">
            <!-- User Card (Visible on Larger Screens) -->
            <div class="hidden md:flex items-center gap-3 bg-gray-50 px-4 py-2 rounded-lg border border-gray-200">
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
                    <div class="w-8 h-8 {{ $bgColor }} rounded-md flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-{{ $icon }} text-white text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800 leading-tight truncate max-w-[150px]">
                            {{ auth()->user()->username ?? 'user' }}
                        </p>
                        <p class="text-xs text-gray-500 leading-tight capitalize">
                            {{ $level }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- User Info Compact (Mobile - sm to md) -->
            <div class="hidden sm:flex md:hidden items-center gap-2 bg-gray-50 px-2 py-1.5 rounded border border-gray-200">
                @php
                    $level = auth()->user()->level ?? 'admin';
                    $bgColor = match(strtolower($level)) {
                        'admin' => 'bg-red-500',
                        'petugas' => 'bg-blue-500',
                        'peminjam' => 'bg-green-500',
                        default => 'bg-gray-500',
                    };
                @endphp
                <div class="w-6 h-6 {{ $bgColor }} rounded flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-{{ $icon }} text-white text-xs"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-800 truncate max-w-[80px]">
                        {{ substr(auth()->user()->username ?? 'user', 0, 8) }}
                    </p>
                    <p class="text-xs text-gray-500 capitalize">
                        {{ substr($level, 0, 3) }}
                    </p>
                </div>
            </div>

            <!-- Mobile Menu Toggle Button -->
            <button 
                onclick="toggleMobileSidebar()" 
                class="sm:hidden text-gray-600 hover:text-gray-900 transition p-1.5 hover:bg-gray-100 rounded-lg flex-shrink-0"
                title="Toggle Menu"
            >
                <i class="fas fa-bars text-base"></i>
            </button>
            
            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button 
                    type="submit" 
                    class="bg-red-500 hover:bg-red-600 text-white px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 rounded-lg font-medium transition-all duration-200 flex items-center gap-1 sm:gap-2 shadow-sm hover:shadow-md text-xs sm:text-sm flex-shrink-0"
                    title="Logout"
                >
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="hidden sm:inline">Logout</span>
                </button>
            </form>
        </div>
    </div>
</header>

<script>
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar?.classList.toggle('hidden');
        overlay?.classList.toggle('hidden');
    }

    // Close sidebar when clicking on a link
    document.querySelectorAll('#sidebar a').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 640) {
                document.getElementById('sidebar')?.classList.add('hidden');
                document.getElementById('sidebar-overlay')?.classList.add('hidden');
            }
        });
    });
</script>