<!DOCTYPE html>
<html class="dark scroll-smooth @if(request()->routeIs('home')) snap-y snap-mandatory scroll-pt-24 @endif" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>@yield('title', 'SaveTube')</title>
    
    <!-- PWA Setup -->
    <link rel="manifest" href="/manifest.json" />
    <meta name="theme-color" content="#1e1e2e" />
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js');
        }
    </script>

    <!-- Alpine.js & Tailwind -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "background": "#1e1e2e",
                        "on-background": "#cdd6f4",
                        "surface": "#1e1e2e",
                        "surface-dim": "#11111b",
                        "surface-bright": "#585b70",
                        "surface-container-lowest": "#11111b",
                        "surface-container-low": "#181825",
                        "surface-container": "#1e1e2e",
                        "surface-container-high": "#313244",
                        "surface-container-highest": "#45475a",
                        "surface-variant": "#313244",
                        "on-surface": "#cdd6f4",
                        "on-surface-variant": "#bac2de",
                        "inverse-surface": "#cdd6f4",
                        "inverse-on-surface": "#1e1e2e",
                        "outline": "#7f849c",
                        "outline-variant": "#585b70",
                        "primary": "#cba6f7",
                        "on-primary": "#11111b",
                        "primary-container": "#b4befe",
                        "on-primary-container": "#11111b",
                        "primary-fixed": "#cba6f7",
                        "primary-fixed-dim": "#b4befe",
                        "on-primary-fixed": "#11111b",
                        "on-primary-fixed-variant": "#1e1e2e",
                        "surface-tint": "#cba6f7",
                        "inverse-primary": "#1e1e2e",
                        "secondary": "#89b4fa",
                        "on-secondary": "#11111b",
                        "secondary-container": "#74c7ec",
                        "on-secondary-container": "#11111b",
                        "secondary-fixed": "#89b4fa",
                        "secondary-fixed-dim": "#74c7ec",
                        "on-secondary-fixed": "#11111b",
                        "on-secondary-fixed-variant": "#1e1e2e",
                        "tertiary": "#a6e3a1",
                        "on-tertiary": "#11111b",
                        "tertiary-container": "#94e2d5",
                        "on-tertiary-container": "#11111b",
                        "tertiary-fixed": "#a6e3a1",
                        "tertiary-fixed-dim": "#94e2d5",
                        "on-tertiary-fixed": "#11111b",
                        "on-tertiary-fixed-variant": "#1e1e2e",
                        "error": "#f38ba8",
                        "on-error": "#11111b",
                        "error-container": "#eba0ac",
                        "on-error-container": "#11111b"
                    },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                    fontFamily: { "headline": ["Inter", "sans-serif"], "body": ["Inter", "sans-serif"], "label": ["Inter", "sans-serif"] }
                }
            }
        }
    </script>
    <style>
        body { background-color: theme('colors.background'); color: theme('colors.on-background'); font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .jetbrains-mono { font-family: 'JetBrains Mono', monospace; }
        .ghost-border { border: 1px solid theme('colors.outline-variant'); border-opacity: 0.15; }
        .glass-panel { background-color: rgba(51, 51, 68, 0.6); backdrop-filter: blur(16px); }
        .cta-gradient { background: linear-gradient(135deg, #e2c7ff, #cba6f7); color: #3f1e66; }
        .cta-gradient:hover { box-shadow: 0 0 20px rgba(203, 166, 247, 0.4); }
        .hover-scale { transition: transform 300ms cubic-bezier(0.4, 0, 0.2, 1); }
        .hover-scale:hover { transform: scale(1.05); }
        .ambient-shadow { box-shadow: 0 4px 40px rgba(227, 224, 247, 0.05); }
        .audio-gradient { background: linear-gradient(135deg, #fab387, #f38ba8); color: #1e1e2e; }
        .card-scale { transition: transform 300ms cubic-bezier(0.4, 0, 0.2, 1); }
        .card-scale:hover { transform: scale(1.01); }
        .input-glow:focus-within { box-shadow: 0 0 0 2px rgba(217, 185, 255, 0.3); }
        @keyframes pulse-glow { 0%, 100% { opacity: 0.4; } 50% { opacity: 1; } }
        .animate-pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col bg-background">
    
    <x-navbar />

    <main class="flex-grow relative z-10 w-full flex flex-col @if(request()->routeIs('home')) @else pt-32 pb-24 px-6 md:px-0 @endif @if(request()->routeIs('history')) max-w-5xl mx-auto gap-12 @endif">
        @yield('content')
    </main>

    <x-footer />
    
    @stack('scripts')
</body>
</html>
