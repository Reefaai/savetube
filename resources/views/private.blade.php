@extends('layouts.app')
@section('title', 'Private Video Downloader - SaveTube')

@section('content')
<div x-data="privateExtractorApp()" x-cloak class="flex flex-col items-center max-w-5xl mx-auto w-full gap-12 pt-12 pb-24 px-6 md:px-0">
    
    {{-- Header Section --}}
    <div class="text-center space-y-4 max-w-3xl pt-8">
        <h1 class="text-4xl max-sm:text-3xl md:text-5xl font-black text-on-background tracking-tight">Private Video Downloader</h1>
        <p class="text-on-surface-variant text-lg max-sm:text-base leading-relaxed">
            Bypass restrictions and preserve digital artifacts from closed platforms.<br class="hidden md:block">
            Follow the source-code extraction method below for secure, client-side media resolution.
        </p>
    </div>

    {{-- Error Alert --}}
    <div x-show="errorMessage" x-transition.opacity.duration.300ms class="w-full max-w-4xl" style="display: none;">
        <div class="flex items-center gap-3 p-4 rounded-xl bg-error-container/20 border border-error/20">
            <span class="material-symbols-outlined text-error">error</span>
            <p class="text-sm text-error font-medium" x-text="errorMessage"></p>
            <button @click="errorMessage = ''" class="ml-auto text-error/60 hover:text-error transition-colors">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
    </div>

    {{-- Steps Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 w-full">
        {{-- Step 1 --}}
        <div class="bg-surface-container-low ghost-border rounded-2xl p-6 shadow-sm hover:border-primary/30 transition-colors">
            <span class="inline-block bg-primary-container text-on-primary-container text-xs font-bold px-3 py-1 rounded-full mb-4 uppercase tracking-wider">Step 1</span>
            <h3 class="text-xl font-bold text-on-surface mb-2">Open Media</h3>
            <p class="text-sm text-on-surface-variant">Navigate to the private video in a new, isolated browser tab.</p>
        </div>
        {{-- Step 2 --}}
        <div class="bg-surface-container-low ghost-border rounded-2xl p-6 shadow-sm hover:border-secondary/30 transition-colors">
            <span class="inline-block bg-secondary-container text-on-secondary-container text-xs font-bold px-3 py-1 rounded-full mb-4 uppercase tracking-wider">Step 2</span>
            <h3 class="text-xl font-bold text-on-surface mb-2">View Source</h3>
            <p class="text-sm text-on-surface-variant">Access the raw page data using <code class="bg-surface-container-high px-1.5 py-0.5 rounded text-primary text-xs">Ctrl+U</code> or <code class="bg-surface-container-high px-1.5 py-0.5 rounded text-primary text-xs">Cmd+Opt+U</code>.</p>
        </div>
        {{-- Step 3 --}}
        <div class="bg-surface-container-low ghost-border rounded-2xl p-6 shadow-sm hover:border-tertiary/30 transition-colors">
            <span class="inline-block bg-tertiary-container text-on-tertiary-container text-xs font-bold px-3 py-1 rounded-full mb-4 uppercase tracking-wider">Step 3</span>
            <h3 class="text-xl font-bold text-on-surface mb-2">Select All</h3>
            <p class="text-sm text-on-surface-variant">Highlight and copy the entire HTML document (<code class="bg-surface-container-high px-1.5 py-0.5 rounded text-primary text-xs">Ctrl+A</code> then <code class="bg-surface-container-high px-1.5 py-0.5 rounded text-primary text-xs">Ctrl+C</code>).</p>
        </div>
        {{-- Step 4 --}}
        <div class="bg-surface-container-low ghost-border rounded-2xl p-6 shadow-sm hover:border-primary/30 transition-colors">
            <span class="inline-block bg-primary/20 text-primary text-xs font-bold px-3 py-1 rounded-full mb-4 uppercase tracking-wider">Step 4</span>
            <h3 class="text-xl font-bold text-on-surface mb-2">Execute</h3>
            <p class="text-sm text-on-surface-variant">Paste the copied code block into the extraction engine below.</p>
        </div>
    </div>

    {{-- Extraction Engine Block --}}
    <div class="w-full bg-surface-container-low ghost-border rounded-3xl p-6 md:p-8 shadow-sm">
        <h2 class="text-xl font-bold text-on-surface mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[20px]">code</span> HTML Source Code
        </h2>
        <form @submit.prevent="extractData">
            <div class="relative w-full mb-6 group">
                <textarea 
                    x-model="sourceCode"
                    placeholder="Paste the <html> code here..." 
                    class="w-full bg-surface-container-lowest border border-outline-variant rounded-xl p-5 font-mono text-sm text-on-surface-variant h-64 focus:border-primary focus:ring-1 focus:ring-primary focus:text-on-surface resize-y custom-scrollbar"
                    required
                ></textarea>
                <div class="absolute inset-0 border-2 border-primary rounded-xl opacity-0 group-focus-within:opacity-20 transition-opacity pointer-events-none"></div>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <label class="text-sm text-on-surface-variant font-medium">Platform:</label>
                    <select x-model="platform" class="bg-surface-container-high border-none rounded-lg text-sm text-on-surface focus:ring-1 focus:ring-primary cursor-pointer w-full sm:w-auto">
                        <option value="facebook">Facebook</option>
                        <option value="instagram">Instagram</option>
                        <option value="tiktok">TikTok</option>
                    </select>
                </div>
                
                <button type="submit" 
                        :disabled="isLoading || !sourceCode.trim()"
                        :class="isLoading ? 'opacity-80 cursor-not-allowed' : 'hover:bg-primary hover:text-on-primary shadow-lg hover:shadow-primary/20'"
                        class="w-full sm:w-auto bg-primary/10 text-primary font-bold py-3 px-6 rounded-xl flex items-center justify-center gap-2 transition-all duration-300">
                    <span class="material-symbols-outlined text-[20px]" x-show="!isLoading">bolt</span>
                    <span class="material-symbols-outlined text-[20px] animate-spin" x-show="isLoading" style="display: none;">progress_activity</span>
                    <span x-text="isLoading ? 'Extracting...' : 'Extract Media'"></span>
                </button>
            </div>
        </form>
    </div>

    {{-- Extraction Result Block (Dynamic, matches home.blade.php) --}}
    <div x-show="showResult" x-transition:enter="ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="w-full mt-4" style="display: none;">
        <h2 class="text-2xl max-sm:text-xl font-bold text-on-surface mb-6 border-b border-white/5 pb-4">Extraction Result</h2>
        
        <div class="flex flex-col md:flex-row gap-8 bg-surface-container-low rounded-2xl p-6 ambient-shadow ghost-border relative overflow-hidden card-scale">
            {{-- Decorative --}}
            <div class="absolute -right-32 -top-32 w-64 h-64 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>

            {{-- Thumbnail (Placeholder for Private Source) --}}
            <div class="w-full md:w-2/5 aspect-video rounded-xl overflow-hidden relative shadow-lg bg-surface flex-shrink-0 z-10">
                <img alt="Private Video Data" class="w-full h-full object-cover mix-blend-luminosity opacity-40" src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?q=80&w=1000&auto=format&fit=crop" />
                <div class="absolute inset-0 bg-gradient-to-t from-background/80 to-transparent"></div>
                <div class="absolute inset-0 flex items-center justify-center flex-col">
                    <span class="material-symbols-outlined text-5xl text-primary/80 mb-2">lock_open</span>
                    <span class="font-mono text-sm text-on-surface font-bold">SOURCE EXTRACTED</span>
                </div>
            </div>

            {{-- Meta & Actions --}}
            <div class="w-full md:w-3/5 flex flex-col justify-between z-10 py-2">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <span class="bg-surface-container-high px-3 py-1 rounded-full text-xs font-mono text-primary-container tracking-wider border border-outline-variant/30 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">lock</span>
                            <span x-text="platform" class="capitalize"></span>
                        </span>
                        <span class="font-mono text-xs text-outline">PRIVATE</span>
                    </div>
                    <h3 class="font-headline font-bold text-2xl max-sm:text-xl text-on-surface mb-2 leading-tight" x-text="videoTitle"></h3>
                    <p class="font-body text-sm text-on-surface-variant line-clamp-2">
                        Data retrieved safely via client-side code extraction.
                    </p>
                </div>

                {{-- Download Buttons --}}
                <div class="mt-8 flex flex-wrap gap-3">
                    <template x-for="(vid, i) in videos" :key="i">
                        <a :href="buildDownloadUrl(vid)"
                           target="_blank" rel="noopener"
                           :class="{
                               'bg-tertiary text-on-tertiary shadow-[0_0_10px_rgba(163,224,159,0.2)]': i === 0,
                               'bg-secondary text-on-secondary': i === 1,
                               'bg-surface-container-high text-on-surface ghost-border': i >= 2,
                           }"
                           class="font-semibold px-5 py-2.5 rounded-lg flex items-center gap-2 hover-scale text-sm">
                            <span class="material-symbols-outlined text-[20px]">download</span>
                            <span x-text="vid.quality"></span>
                        </a>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Custom scrollbar for textarea to match Catppuccin theme */
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: theme('colors.surface-container-highest');
        border-radius: 20px;
    }
</style>
@endpush

@push('scripts')
<script>
function privateExtractorApp() {
    return {
        sourceCode: '',
        isLoading: false,
        showResult: false,
        errorMessage: '',
        videos: [],
        platform: 'facebook',
        videoTitle: 'Private Video (Source Code)',

        async extractData() {
            if (!this.sourceCode.trim()) return;
            
            this.isLoading = true;
            this.showResult = false;
            this.errorMessage = '';
            
            try {
                const response = await fetch('{{ route('video.extract-private') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        html: this.sourceCode,
                        platform: this.platform,
                    }),
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.videos = data.videos;
                    this.platform = data.platform;
                    this.showResult = true;
                    setTimeout(() => {
                        window.scrollBy({ top: 400, behavior: 'smooth' });
                    }, 150);
                } else {
                    this.errorMessage = data.message || 'Gagal mengekstrak video. Pastikan source code valid dan berisi URL media.';
                }
            } catch (err) {
                this.errorMessage = 'Terjadi kesalahan jaringan saat memproses permintaan.';
                console.error('Extraction error:', err);
            } finally {
                this.isLoading = false;
            }
        },

        buildDownloadUrl(vid) {
            const base = '{{ route('video.download') }}';
            const params = new URLSearchParams({
                url: vid.url,
                title: this.videoTitle,
                ext: 'mp4',
            });
            return `${base}?${params.toString()}`;
        }
    };
}
</script>
@endpush
@endsection
