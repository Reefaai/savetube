@php
    $currentRoute = request()->route()?->getName();
@endphp

<nav :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="bg-surface-container-low/95 dark:bg-surface-container-low/95 backdrop-blur-xl h-screen w-64 fixed left-0 top-0 border-r border-[#4a444f]/15 shadow-[20px_0_40px_rgba(0,0,0,0.3)] flex flex-col py-8 z-50 transition-transform duration-300 ease-in-out md:translate-x-0">
    <!-- Brand -->
    <div class="px-6 mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-primary mb-1">SaveTube Admin</h1>
            <p class="text-xs text-on-surface-variant opacity-70 font-medium">Ethereal Archive Control</p>
        </div>
        <!-- Close Button Mobile -->
        <button @click="sidebarOpen = false" class="md:hidden text-on-surface-variant hover:text-primary transition-colors">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    
    <!-- Navigation Links -->
    <div class="flex-1 px-4 space-y-1 overflow-y-auto">
        {{-- Overview --}}
        <a class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 ease-out hover:scale-[1.02] active:scale-[0.98]
            {{ $currentRoute === 'admin.dashboard' 
                ? 'text-primary font-bold border-r-2 border-primary bg-surface-container-high/50' 
                : 'text-on-surface-variant opacity-70 hover:bg-surface-container-high hover:opacity-100' }}"
           href="{{ route('admin.dashboard') }}">
            <span class="material-symbols-outlined text-xl" @if($currentRoute === 'admin.dashboard') style="font-variation-settings: 'FILL' 1;" @endif>dashboard</span>
            <span class="font-['Inter'] tracking-tighter text-sm">Overview</span>
        </a>

        {{-- User Management --}}
        <a class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 ease-out hover:scale-[1.02] active:scale-[0.98]
            {{ $currentRoute === 'admin.users' 
                ? 'text-primary font-bold border-r-2 border-primary bg-surface-container-high/50' 
                : 'text-on-surface-variant opacity-70 hover:bg-surface-container-high hover:opacity-100' }}"
           href="{{ route('admin.users') }}">
            <span class="material-symbols-outlined text-xl" @if($currentRoute === 'admin.users') style="font-variation-settings: 'FILL' 1;" @endif>group</span>
            <span class="font-['Inter'] tracking-tighter text-sm font-medium">User Management</span>
        </a>

        {{-- Back to Site --}}
        <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant opacity-70 hover:bg-surface-container-high hover:opacity-100 transition-all duration-300 ease-out hover:scale-[1.02] active:scale-[0.98]" href="{{ route('home') }}">
            <span class="material-symbols-outlined text-xl">home</span>
            <span class="font-['Inter'] tracking-tighter text-sm font-medium">Kembali ke Situs</span>
        </a>
    </div>
    
    <!-- Footer / CTA -->
    <div class="px-4 mt-auto pt-6 border-t border-outline-variant/15">
        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-error/80 hover:bg-error/10 hover:text-error transition-all duration-300 ease-out hover:scale-[1.02] active:scale-[0.98]">
                <span class="material-symbols-outlined text-xl">logout</span>
                <span class="font-['Inter'] tracking-tighter text-sm font-medium">Logout</span>
            </button>
        </form>

        <!-- Admin Info -->
        <div class="mt-6 px-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-sm ring-1 ring-primary/30">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="flex flex-col min-w-0">
                <span class="text-sm font-medium text-on-surface truncate">{{ Auth::user()->name }}</span>
                <span class="text-xs text-on-surface-variant font-mono truncate">{{ Auth::user()->email }}</span>
            </div>
        </div>
    </div>
</nav>
