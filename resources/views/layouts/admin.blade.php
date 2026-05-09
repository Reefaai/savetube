<!DOCTYPE html>
<html class="dark" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>@yield('title', 'SaveTube Admin - Overview')</title>
<meta name="csrf-token" content="{{ csrf_token() }}"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                "colors": {
                    "on-tertiary-fixed": "#002205",
                    "surface-tint": "#d9b9ff",
                    "secondary-container": "#3b457c",
                    "on-secondary": "#212c62",
                    "surface-container-low": "#1a1a2a",
                    "inverse-primary": "#704f98",
                    "surface-bright": "#383849",
                    "on-primary": "#3f1e66",
                    "on-primary-fixed-variant": "#57377f",
                    "secondary-fixed": "#dee0ff",
                    "surface-container": "#1e1e2e",
                    "surface-container-lowest": "#0d0d1c",
                    "primary-container": "#cba6f7",
                    "tertiary-fixed": "#b4f2af",
                    "on-surface": "#e3e0f7",
                    "on-tertiary-fixed-variant": "#19511f",
                    "inverse-surface": "#e3e0f7",
                    "on-surface-variant": "#cdc3d1",
                    "primary": "#e2c7ff",
                    "error-container": "#93000a",
                    "on-secondary-fixed-variant": "#38437a",
                    "on-background": "#e3e0f7",
                    "surface-container-highest": "#333344",
                    "surface-dim": "#121221",
                    "secondary-fixed-dim": "#bac3ff",
                    "on-error": "#690005",
                    "surface": "#121221",
                    "primary-fixed-dim": "#d9b9ff",
                    "inverse-on-surface": "#2f2f40",
                    "error": "#ffb4ab",
                    "surface-variant": "#333344",
                    "primary-fixed": "#eedbff",
                    "on-secondary-container": "#abb5f4",
                    "on-primary-fixed": "#290350",
                    "on-primary-container": "#57377f",
                    "background": "#121221",
                    "surface-container-high": "#292839",
                    "outline": "#968e9a",
                    "tertiary-fixed-dim": "#99d595",
                    "on-tertiary": "#00390c",
                    "tertiary-container": "#89c485",
                    "on-secondary-fixed": "#09154c",
                    "on-tertiary-container": "#1a5220",
                    "outline-variant": "#4a444f",
                    "on-error-container": "#ffdad6",
                    "tertiary": "#a3e09f",
                    "secondary": "#bac3ff"
                },
                "borderRadius": {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px"
                },
                "fontFamily": {
                    "headline": ["Inter"],
                    "body": ["Inter"],
                    "label": ["Inter"],
                    "mono": ["JetBrains Mono"]
                }
            }
        }
    }
</script>
<style>
    body {
        font-family: 'Inter', sans-serif;
    }
    .font-mono {
        font-family: 'JetBrains Mono', monospace;
    }
    /* Hide scrollbar for cleanly designed sidebar */
    ::-webkit-scrollbar {
        width: 6px;
    }
    ::-webkit-scrollbar-track {
        background: transparent;
    }
    ::-webkit-scrollbar-thumb {
        background: #333344;
        border-radius: 4px;
    }
</style>
</head>
<body class="bg-surface text-on-surface min-h-screen selection:bg-primary-container/30 selection:text-primary" x-data="{ sidebarOpen: false }">

    <!-- Sidebar Component -->
    <x-admin.sidebar />

    <!-- Backdrop for Mobile Sidebar -->
    <div x-cloak x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity class="fixed inset-0 bg-black/60 z-40 md:hidden" style="display: none;"></div>

    <!-- TopAppBar -->
    <header class="bg-[#11111b]/60 dark:bg-[#11111b]/60 backdrop-blur-md fixed top-0 right-0 w-full md:w-[calc(100%-16rem)] z-30 flex justify-between items-center px-6 md:px-10 h-20 md:ml-64 bg-[#1e1e2e] transition-all duration-200 border-b border-outline-variant/15">
        <div class="flex items-center gap-4 md:gap-6">
            <!-- Mobile Sidebar Toggle -->
            <button @click="sidebarOpen = true" class="md:hidden text-[#a6adc8] hover:text-[#f5e0dc] p-2 rounded-lg hover:bg-surface-variant/50 transition-colors">
                <span class="material-symbols-outlined">menu</span>
            </button>
            
            <h2 class="text-lg md:text-xl font-['Inter'] font-semibold tracking-tight text-[#cba6f7]">@yield('header_title', 'Dashboard Overview')</h2>
            
            <!-- Maintenance Mode Toggle -->
            <div x-data="maintenanceToggle()" class="hidden md:flex items-center gap-3 ml-8 px-4 py-2 rounded-full border transition-all duration-300"
                 :class="isMaintenance ? 'bg-error/10 border-error/30' : 'bg-surface-container-high border-outline-variant/15'">
                <span class="text-sm font-medium transition-colors duration-300"
                      :class="isMaintenance ? 'text-error' : 'text-on-surface-variant'">
                    <span class="material-symbols-outlined text-[16px] align-middle mr-1" x-text="isMaintenance ? 'warning' : 'build'"></span>
                    Maintenance Mode
                </span>
                <button @click="toggle()" :disabled="isToggling"
                        :aria-checked="isMaintenance.toString()"
                        class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-surface disabled:opacity-50 disabled:cursor-wait"
                        :class="isMaintenance ? 'bg-error' : 'bg-surface-container-highest'" role="switch" type="button">
                    <span aria-hidden="true"
                          class="pointer-events-none inline-block h-4 w-4 transform rounded-full shadow ring-0 transition duration-200 ease-in-out"
                          :class="isMaintenance ? 'translate-x-4 bg-on-error' : 'translate-x-0 bg-outline'"></span>
                </button>
                <!-- Toast Notification -->
                <div x-show="toastMessage" x-transition.opacity.duration.300ms
                     class="fixed top-24 right-6 z-50 px-4 py-3 rounded-xl border shadow-lg backdrop-blur-xl max-w-sm"
                     :class="toastSuccess ? 'bg-tertiary/10 border-tertiary/30 text-tertiary' : 'bg-error/10 border-error/30 text-error'"
                     style="display: none;">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg" x-text="toastSuccess ? 'check_circle' : 'error'"></span>
                        <span class="text-sm font-medium" x-text="toastMessage"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2 md:gap-4">
            <button class="text-[#a6adc8] hover:text-[#f5e0dc] transition-colors p-2 rounded-full hover:bg-surface-variant/50">
                <span class="material-symbols-outlined">notifications</span>
            </button>
            <button class="text-[#a6adc8] hover:text-[#f5e0dc] transition-colors p-2 rounded-full hover:bg-surface-variant/50">
                <span class="material-symbols-outlined">help_outline</span>
            </button>
        </div>
    </header>

    <!-- Main Content Canvas -->
    <main class="md:ml-64 pt-28 px-4 md:px-10 pb-20 transition-all duration-300">
        @yield('content')
    </main>

<script>
function maintenanceToggle() {
    return {
        isMaintenance: @json($isMaintenance ?? false),
        isToggling: false,
        toastMessage: '',
        toastSuccess: true,

        async toggle() {
            this.isToggling = true;
            try {
                const response = await fetch('{{ route("admin.toggleMaintenance") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                const data = await response.json();
                if (data.success) {
                    this.isMaintenance = data.maintenance;
                    this.showToast(data.message, true);
                } else {
                    this.showToast('Gagal mengubah maintenance mode.', false);
                }
            } catch (err) {
                console.error('Maintenance toggle error:', err);
                this.showToast('Gagal terhubung ke server.', false);
            } finally {
                this.isToggling = false;
            }
        },

        showToast(message, success) {
            this.toastMessage = message;
            this.toastSuccess = success;
            setTimeout(() => { this.toastMessage = ''; }, 3000);
        },
    };
}
</script>
</body>
</html>

