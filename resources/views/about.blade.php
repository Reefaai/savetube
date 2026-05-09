@extends('layouts.app')

@section('title', 'Tentang SaveTube')

@section('content')

<!-- Content Wrapper -->
<div class="w-full max-w-5xl mx-auto flex flex-col gap-20 md:px-12 py-16 relative z-10">
    
    <!-- Hero Section: Centered & Airy -->
    <section class="flex flex-col items-center text-center gap-8 fade-in-up snap-start">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-surface-container-high ghost-border mb-2 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
            <span class="text-xs font-mono text-on-surface-variant tracking-widest uppercase">v0.1.0 - Snapshot Release</span>
        </div>
        
        <h1 class="text-5xl md:text-7xl font-black tracking-tighter text-on-surface leading-[1.1]">
            The Vision Behind <br/>
            <span class="bg-clip-text text-transparent bg-gradient-to-r from-primary via-secondary to-tertiary">SaveTube</span>
        </h1>
        <p class="text-xl md:text-2xl text-on-surface-variant leading-relaxed max-w-3xl font-light">
            A highly-curated, zero-friction archive tool designed to preserve digital artifacts from the social web. Built for speed, permanence, and aesthetic clarity.
        </p>
    </section>

    <!-- Divider/Flow Element -->
    <div class="w-full flex justify-center opacity-60">
        <div class="w-[1px] h-24 bg-gradient-to-b from-primary/40 to-transparent"></div>
    </div>

    <!-- Tech Stack: Solid Cards -->
    <section class="flex flex-col items-center gap-12 relative fade-in-up snap-start" style="animation-delay: 150ms;">
        <div class="text-center">
            <h2 class="text-3xl font-bold tracking-tight text-on-surface mb-4">Engineered for Permanence</h2>
            <p class="text-on-surface-variant max-w-xl mx-auto text-lg font-light">Built upon a robust, modern foundation to ensure maximum throughput and reliability during peak extraction events.</p>
        </div>
        
        <div class="flex flex-wrap justify-center gap-5 max-w-4xl">
            <!-- Laravel Bubble -->
            <div class="group flex items-center gap-4 px-6 py-4 rounded-2xl bg-surface-container-low ghost-border hover:-translate-y-1 hover:shadow-lg hover:shadow-[#f38ba8]/10 hover:bg-surface-container transition-all duration-300 cursor-default">
                <div class="w-12 h-12 rounded-xl bg-[#f38ba8]/10 text-[#f38ba8] flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <span class="material-symbols-outlined">code_blocks</span>
                </div>
                <div class="flex flex-col text-left">
                    <span class="font-bold text-on-surface text-lg leading-tight">Laravel</span>
                    <span class="text-xs text-on-surface-variant font-medium">Core Framework</span>
                </div>
            </div>
            
            <!-- PHP Bubble -->
            <div class="group flex items-center gap-4 px-6 py-4 rounded-2xl bg-surface-container-low ghost-border hover:-translate-y-1 hover:shadow-lg hover:shadow-[#89b4fa]/10 hover:bg-surface-container transition-all duration-300 cursor-default">
                <div class="w-12 h-12 rounded-xl bg-[#89b4fa]/10 text-[#89b4fa] flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <span class="material-symbols-outlined">terminal</span>
                </div>
                <div class="flex flex-col text-left">
                    <span class="font-bold text-on-surface text-lg leading-tight">PHP</span>
                    <span class="text-xs text-on-surface-variant font-medium">Runtime Environment</span>
                </div>
            </div>

            <!-- MySQL Bubble -->
            <div class="group flex items-center gap-4 px-6 py-4 rounded-2xl bg-surface-container-low ghost-border hover:-translate-y-1 hover:shadow-lg hover:shadow-[#89dceb]/10 hover:bg-surface-container transition-all duration-300 cursor-default">
                <div class="w-12 h-12 rounded-xl bg-[#89dceb]/10 text-[#89dceb] flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <span class="material-symbols-outlined">database</span>
                </div>
                <div class="flex flex-col text-left">
                    <span class="font-bold text-on-surface text-lg leading-tight">MySQL</span>
                    <span class="text-xs text-on-surface-variant font-medium">Relational Database</span>
                </div>
            </div>

            <!-- yt-dlp Bubble -->
            <div class="group flex items-center gap-4 px-6 py-4 rounded-2xl bg-surface-container-low ghost-border hover:-translate-y-1 hover:shadow-lg hover:shadow-[#cba6f7]/10 hover:bg-surface-container transition-all duration-300 cursor-default">
                <div class="w-12 h-12 rounded-xl bg-[#cba6f7]/10 text-[#cba6f7] flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <span class="material-symbols-outlined">download</span>
                </div>
                <div class="flex flex-col text-left">
                    <span class="font-bold text-on-surface text-lg leading-tight">yt-dlp</span>
                    <span class="text-xs text-on-surface-variant font-medium">Extraction Engine</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Divider/Flow Element -->
    <div class="w-full flex justify-center opacity-60">
        <div class="w-[1px] h-24 bg-gradient-to-b from-transparent via-secondary/40 to-transparent"></div>
    </div>

    <!-- Developer Section: Solid Profile -->
    <section class="flex flex-col md:flex-row items-center gap-12 max-w-4xl mx-auto w-full relative fade-in-up snap-start" style="animation-delay: 300ms;">
        
        <div class="w-56 h-56 rounded-[2.5rem] p-2 flex-shrink-0 relative group transform rotate-3 hover:rotate-0 transition-transform duration-700 bg-surface-container-low ghost-border shadow-xl">
            <img src="https://raw.githubusercontent.com/catppuccin/catppuccin/main/assets/logos/social/pride.png" alt="Ahmad Rifa'i" class="w-full h-full object-cover rounded-[2rem] relative z-10 bg-surface-container-lowest" />
        </div>
        
        <div class="flex flex-col gap-6 text-center md:text-left flex-1 z-10">
            <div>
                <div class="inline-flex items-center gap-2 mb-4 justify-center md:justify-start">
                    <span class="material-symbols-outlined text-secondary text-sm">badge</span>
                    <span class="text-xs font-mono text-secondary tracking-widest uppercase font-semibold">System Architect</span>
                </div>
                <h3 class="text-4xl md:text-5xl font-black text-on-surface tracking-tight mb-4">Ahmad Rifa`i</h3>
                <p class="text-on-surface-variant text-lg leading-relaxed font-light">
                    Crafting elegant solutions for complex digital preservation challenges. Focused on the intersection of intuitive human-computer interaction and robust backend architecture.
                </p>
            </div>
            
            <div class="flex flex-wrap justify-center md:justify-start gap-3 mt-2">
                <div class="flex items-center gap-3 bg-surface-container-low ghost-border px-4 py-2.5 rounded-xl transition-colors hover:bg-surface-container cursor-default">
                    <div class="w-8 h-8 rounded-lg bg-surface-container-highest flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-sm">id_card</span>
                    </div>
                    <div class="flex flex-col text-left">
                        <span class="text-[9px] text-on-surface-variant uppercase tracking-widest font-bold">NIM</span>
                        <span class="text-sm font-mono text-on-surface font-semibold">42240169</span>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 bg-surface-container-low ghost-border px-4 py-2.5 rounded-xl transition-colors hover:bg-surface-container cursor-default">
                    <div class="w-8 h-8 rounded-lg bg-surface-container-highest flex items-center justify-center">
                        <span class="material-symbols-outlined text-secondary text-sm">school</span>
                    </div>
                    <div class="flex flex-col text-left">
                        <span class="text-[9px] text-on-surface-variant uppercase tracking-widest font-bold">Class</span>
                        <span class="text-sm font-mono text-on-surface font-semibold">RPL-2024-KIP-P2</span>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 bg-surface-container-low ghost-border px-4 py-2.5 rounded-xl transition-colors hover:bg-surface-container cursor-default w-full md:w-auto">
                    <div class="w-8 h-8 rounded-lg bg-surface-container-highest flex items-center justify-center">
                        <span class="material-symbols-outlined text-tertiary text-sm">menu_book</span>
                    </div>
                    <div class="flex flex-col text-left">
                        <span class="text-[9px] text-on-surface-variant uppercase tracking-widest font-bold">Course</span>
                        <span class="text-sm font-mono text-on-surface font-semibold">Interaksi Manusia & Komputer</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Custom Keyframes for Animations -->
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .fade-in-up {
        opacity: 0;
        animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>
@endsection
