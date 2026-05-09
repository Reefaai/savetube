<div x-data="{ mobileMenuOpen: false }" @keydown.escape.window="mobileMenuOpen = false">
    <header class="fixed top-0 left-0 right-0 z-50 border-b border-white/5 bg-surface-container-low/60 backdrop-blur-xl transition-all duration-300 shadow-[0_4px_40px_rgba(227,224,247,0.05)]">
        <div class="flex items-center justify-between w-full px-6 py-4 max-w-7xl mx-auto relative">
            <a class="text-2xl font-black text-primary tracking-tighter" href="{{ route('home') }}">SaveTube</a>
            
            <nav class="hidden md:flex items-center gap-8 absolute left-1/2 -translate-x-1/2">
                <a class="text-on-surface-variant hover:text-primary transition-colors font-medium @if(request()->routeIs('home')) text-primary border-b-2 border-primary pb-1 @endif" href="{{ route('home') }}">Home</a>
                @auth
                <a class="text-on-surface-variant hover:text-primary transition-colors font-medium @if(request()->routeIs('history')) text-primary border-b-2 border-primary pb-1 @endif" href="{{ route('history') }}">History</a>
                @if(Auth::user()->isAdmin())
                <a class="text-tertiary hover:text-tertiary-fixed transition-colors font-medium" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                @endif
                @endauth
                <a class="text-on-surface-variant hover:text-primary transition-colors font-medium @if(request()->routeIs('about')) text-primary border-b-2 border-primary pb-1 @endif" href="{{ route('about') }}">About</a>
            </nav>
            
            <div class="flex items-center gap-4 md:gap-6">
                @auth
                    {{-- Logged-in state --}}
                    <div class="hidden md:flex items-center gap-4">
                        @if(Auth::user()->isAdmin())
                            <span class="flex text-sm font-bold text-tertiary items-center gap-2">
                                {{ Auth::user()->name }} (Admin)
                                <img src="https://raw.githubusercontent.com/catppuccin/catppuccin/main/assets/logos/exports/1544x1544_circle.png" alt="Admin Avatar" class="w-8 h-8 rounded-full shadow-sm border-2 border-tertiary">
                            </span>
                        @else
                            <span class="flex text-sm font-medium text-on-surface-variant items-center gap-2">
                                {{ Auth::user()->name }}
                                <img src="https://raw.githubusercontent.com/catppuccin/catppuccin/main/assets/logos/exports/1544x1544_circle.png" alt="User Avatar" class="w-8 h-8 rounded-full shadow-sm border border-primary/20">
                            </span>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="flex items-center">
                            @csrf
                            <button type="submit" title="Keluar" class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center text-on-surface-variant hover:text-error hover:bg-error-container/20 transition-colors">
                                <span class="material-symbols-outlined text-[18px]">logout</span>
                            </button>
                        </form>
                    </div>
                @else
                    {{-- Guest state --}}
                    <div class="hidden md:flex items-center gap-3">
                        <a href="{{ route('login') }}" class="text-sm font-medium text-on-surface-variant hover:text-primary transition-colors items-center h-10 flex">Login</a>
                        <a href="{{ route('register') }}" title="Daftar" class="flex items-center justify-center hover:scale-105 transition-transform">
                            <div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center overflow-hidden border border-white/10">
                                <img src="https://raw.githubusercontent.com/catppuccin/catppuccin/main/assets/logos/exports/1544x1544_circle.png" alt="Guest Avatar" class="w-full h-full object-cover opacity-50 grayscale">
                            </div>
                        </a>
                    </div>
                @endauth

                {{-- Mobile Menu Toggle --}}
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-on-surface-variant hover:text-primary focus:outline-none flex items-center justify-center w-10 h-10 rounded-full bg-surface-container-high/50 transition-colors">
                    <span class="material-symbols-outlined" x-text="mobileMenuOpen ? 'close' : 'menu'">menu</span>
                </button>
            </div>
        </div>

        {{-- Mobile Menu Dropdown --}}
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="md:hidden absolute top-full left-0 right-0 bg-surface-container-low/95 backdrop-blur-xl border-b border-white/5 shadow-xl" style="display: none;">
            <div class="flex flex-col px-6 py-6 gap-4">
                <a class="text-on-surface-variant hover:text-primary transition-colors font-medium @if(request()->routeIs('home')) text-primary @endif" href="{{ route('home') }}">Home</a>
                @auth
                <a class="text-on-surface-variant hover:text-primary transition-colors font-medium @if(request()->routeIs('history')) text-primary @endif" href="{{ route('history') }}">History</a>
                @if(Auth::user()->isAdmin())
                <a class="text-tertiary hover:text-tertiary-fixed transition-colors font-medium" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                @endif
                @endauth
                <a class="text-on-surface-variant hover:text-primary transition-colors font-medium @if(request()->routeIs('about')) text-primary @endif" href="{{ route('about') }}">About</a>
                
                <hr class="border-white/5 my-2">
                
                @auth
                    @if(Auth::user()->isAdmin())
                        <div class="flex items-center gap-3 mb-2 text-tertiary bg-tertiary/10 p-3 rounded-xl border border-tertiary/20">
                            <img src="https://raw.githubusercontent.com/catppuccin/catppuccin/main/assets/logos/exports/1544x1544_circle.png" alt="Admin Avatar" class="w-10 h-10 rounded-full shadow-sm border-2 border-tertiary">
                            <div class="flex flex-col">
                                <span class="font-bold text-lg leading-tight">{{ Auth::user()->name }}</span>
                                <span class="text-xs font-medium opacity-80">Administrator</span>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-3 mb-2 text-on-surface-variant bg-surface-container-high/30 p-3 rounded-xl border border-white/5">
                            <img src="https://raw.githubusercontent.com/catppuccin/catppuccin/main/assets/logos/exports/1544x1544_circle.png" alt="User Avatar" class="w-10 h-10 rounded-full shadow-sm border border-primary/20">
                            <span class="font-medium text-lg">{{ Auth::user()->name }}</span>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="block w-full">
                        @csrf
                        <button type="submit" class="w-full text-left font-medium text-error hover:bg-error/10 transition-colors flex items-center gap-2 py-3 px-4 rounded-xl">
                            <span class="material-symbols-outlined">logout</span>
                            Keluar
                        </button>
                    </form>
                @else
                    <div class="flex items-center gap-3 mb-4 text-on-surface-variant bg-surface-container-high/30 p-3 rounded-xl border border-white/5">
                        <img src="https://raw.githubusercontent.com/catppuccin/catppuccin/main/assets/logos/exports/1544x1544_circle.png" alt="Guest Avatar" class="w-10 h-10 rounded-full shadow-sm opacity-50 grayscale">
                        <span class="font-medium">Guest User</span>
                    </div>
                    <a href="{{ route('login') }}" class="w-full flex justify-center py-3 rounded-xl bg-surface-container-high text-on-surface-variant hover:text-primary transition-colors font-medium shadow-sm">Login</a>
                    <a href="{{ route('register') }}" class="w-full flex justify-center py-3 rounded-xl bg-gradient-to-r from-primary to-primary-container text-on-primary font-bold mt-2 shadow-md">Daftar</a>
                @endauth
            </div>
        </div>
    </header>
</div>
