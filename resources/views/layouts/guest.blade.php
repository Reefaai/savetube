<!DOCTYPE html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>@yield('title', 'SaveTube')</title>
    
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
                        "on-surface-variant": "#cdc3d1",
                        "on-primary-container": "#57377f",
                        "surface-dim": "#121221",
                        "surface-variant": "#333344",
                        "on-secondary-fixed": "#09154c",
                        "tertiary-fixed": "#b4f2af",
                        "outline-variant": "#4a444f",
                        "on-surface": "#e3e0f7",
                        "on-background": "#e3e0f7",
                        "on-primary-fixed": "#290350",
                        "on-error": "#690005",
                        "surface-container-highest": "#333344",
                        "on-primary-fixed-variant": "#57377f",
                        "surface-bright": "#383849",
                        "on-secondary-fixed-variant": "#38437a",
                        "surface": "#121221",
                        "surface-container-lowest": "#0d0d1c",
                        "primary": "#e2c7ff",
                        "primary-container": "#cba6f7",
                        "error-container": "#93000a",
                        "on-primary": "#3f1e66",
                        "surface-container-high": "#292839",
                        "error": "#ffb4ab",
                        "inverse-primary": "#704f98",
                        "on-tertiary": "#00390c",
                        "tertiary-fixed-dim": "#99d595",
                        "surface-container-low": "#1a1a2a",
                        "primary-fixed": "#eedbff",
                        "on-secondary": "#212c62",
                        "primary-fixed-dim": "#d9b9ff",
                        "inverse-on-surface": "#2f2f40",
                        "surface-container": "#1e1e2e",
                        "on-tertiary-fixed-variant": "#19511f",
                        "secondary-fixed": "#dee0ff",
                        "tertiary-container": "#89c485",
                        "secondary-container": "#3b457c",
                        "secondary-fixed-dim": "#bac3ff",
                        "inverse-surface": "#e3e0f7",
                        "on-tertiary-fixed": "#002205",
                        "secondary": "#bac3ff",
                        "on-secondary-container": "#abb5f4",
                        "surface-tint": "#d9b9ff",
                        "outline": "#968e9a",
                        "tertiary": "#a3e09f",
                        "on-error-container": "#ffdad6",
                        "background": "#121221",
                        "on-tertiary-container": "#1a5220"
                    },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                    fontFamily: { "headline": ["Inter", "sans-serif"], "body": ["Inter", "sans-serif"], "label": ["Inter", "sans-serif"], "mono": ["JetBrains Mono", "monospace"] }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #11111b; color: #e3e0f7; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .glass-panel { background-color: rgba(51, 51, 68, 0.6); backdrop-filter: blur(16px); border: 1px solid rgba(74, 68, 79, 0.15); }
        .input-focus-pulse:focus { box-shadow: 0 0 0 2px rgba(217, 185, 255, 0.5); border-color: rgba(74, 68, 79, 0.5); }
    </style>
    @stack('styles')
</head>
<body class="font-body text-on-surface antialiased min-h-screen grid place-items-center relative bg-[#11111b] py-12">

    <main class="relative z-10 w-full max-w-[440px] px-6">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
