@push('styles')
<style>
    .glass-overlay { background-color: rgba(18, 18, 33, 0.8); backdrop-filter: blur(16px); }
    .glass-card { background-color: rgba(51, 51, 68, 0.6); backdrop-filter: blur(16px); border: 1px solid rgba(74, 68, 79, 0.15); box-shadow: 0 4px 40px rgba(227, 224, 247, 0.05); }
    .gradient-btn { background: linear-gradient(135deg, #e2c7ff, #cba6f7); color: #3f1e66; transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .gradient-btn:hover { transform: scale(1.05); }
    .ghost-input { background-color: #0d0d1c; border: 1px solid rgba(74, 68, 79, 0.15); transition: all 0.3s ease; }
    .ghost-input:focus { border-color: rgba(74, 68, 79, 0.5); box-shadow: 0 0 0 2px rgba(217, 185, 255, 0.2); outline: none; }
</style>
@endpush

{{-- Private Video Modal Container --}}
<div x-data="privateModalApp()"
     @open-private-modal.window="openModal($event.detail)"
     x-show="showPrivateModal"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
     style="display: none;">

    {{-- Overlay Backdrop --}}
    <div x-show="showPrivateModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="absolute inset-0 glass-overlay"
         @click="showPrivateModal = false"></div>

    {{-- Modal Card --}}
    <div x-show="showPrivateModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="w-full max-w-2xl glass-card rounded-2xl overflow-hidden flex flex-col max-h-[90vh] relative z-10 shadow-2xl">
        
        {{-- Modal Header --}}
        <div class="p-6 md:p-8 pb-4 flex items-start gap-4 border-b border-outline-variant/10">
            <div class="w-12 h-12 rounded-full bg-warning/10 flex items-center justify-center flex-shrink-0 text-warning">
                <span class="material-symbols-outlined text-3xl">warning</span>
            </div>
            <div class="flex-grow">
                <h2 class="text-2xl font-headline font-bold text-on-surface tracking-tight">Video Privat Terdeteksi</h2>
                <p class="text-on-surface-variant mt-2 text-sm leading-relaxed">
                    Tautan ini mengarah ke konten privat. Untuk mengunduh, Anda perlu memberikan kode sumber (HTML) dari halaman video tersebut.
                </p>
            </div>
            <button @click="showPrivateModal = false" class="text-on-surface-variant hover:text-on-surface transition-colors p-1 flex-shrink-0 rounded-full hover:bg-surface-variant/50 focus:outline-none">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        {{-- Modal Body (Scrollable) --}}
        <div class="p-6 md:p-8 overflow-y-auto custom-scrollbar flex-grow">
            {{-- Steps --}}
            <div class="mb-8 space-y-4">
                <h3 class="text-sm font-semibold text-primary uppercase tracking-widest mb-4">Cara Ekstrak</h3>
                <ol class="space-y-3">
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-surface-container-high text-primary-container flex items-center justify-center text-xs font-mono flex-shrink-0 mt-0.5">1</span>
                        <span class="text-on-surface-variant text-sm">Buka tautan video di tab baru peramban (browser) Anda.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-surface-container-high text-primary-container flex items-center justify-center text-xs font-mono flex-shrink-0 mt-0.5">2</span>
                        <span class="text-on-surface-variant text-sm">Pastikan Anda sudah login ke akun yang memiliki akses ke video tersebut.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-surface-container-high text-primary-container flex items-center justify-center text-xs font-mono flex-shrink-0 mt-0.5">3</span>
                        <span class="text-on-surface-variant text-sm">Tekan <kbd class="px-1.5 py-0.5 bg-surface-container-highest rounded border border-outline-variant/30 font-mono text-xs text-on-surface mx-1">Ctrl</kbd> + <kbd class="px-1.5 py-0.5 bg-surface-container-highest rounded border border-outline-variant/30 font-mono text-xs text-on-surface mx-1">U</kbd> (Windows) atau <kbd class="px-1.5 py-0.5 bg-surface-container-highest rounded border border-outline-variant/30 font-mono text-xs text-on-surface mx-1">Cmd</kbd> + <kbd class="px-1.5 py-0.5 bg-surface-container-highest rounded border border-outline-variant/30 font-mono text-xs text-on-surface mx-1">Option</kbd> + <kbd class="px-1.5 py-0.5 bg-surface-container-highest rounded border border-outline-variant/30 font-mono text-xs text-on-surface mx-1">U</kbd> (Mac) untuk melihat Page Source.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-surface-container-high text-primary-container flex items-center justify-center text-xs font-mono flex-shrink-0 mt-0.5">4</span>
                        <span class="text-on-surface-variant text-sm">Salin semua kode yang muncul dan tempelkan pada kotak di bawah ini.</span>
                    </li>
                </ol>
            </div>

            {{-- Textarea --}}
            <div class="space-y-2">
                <label class="text-sm font-semibold text-primary uppercase tracking-widest flex items-center gap-2" for="html-source">
                    <span class="material-symbols-outlined text-sm">code</span>
                    HTML Source
                </label>
                <textarea x-model="sourceCode" class="w-full p-4 ghost-input rounded-xl text-on-surface font-mono text-sm leading-relaxed resize-y min-h-[120px]" id="html-source" placeholder='<!DOCTYPE html>&#10;<html lang="en">&#10;...' rows="6"></textarea>
            </div>
            
            {{-- Extract Error --}}
            <div x-show="extractError" x-transition class="mt-4 p-3 rounded-lg bg-error-container/20 border border-error/20" style="display: none;">
                <p class="text-sm text-error" x-text="extractError"></p>
            </div>

            {{-- Extract Success --}}
            <div x-show="extractedVideos.length > 0" x-transition class="mt-4 space-y-3" style="display: none;">
                <h4 class="text-sm font-semibold text-tertiary uppercase tracking-widest flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">check_circle</span>
                    Video Ditemukan
                </h4>
                <template x-for="(vid, idx) in extractedVideos" :key="idx">
                    <a :href="vid.url" target="_blank" rel="noopener"
                       class="flex items-center gap-3 p-3 rounded-lg bg-surface-container-lowest ghost-border hover:bg-surface-container transition-colors group">
                        <span class="material-symbols-outlined text-tertiary">download</span>
                        <div class="flex-grow">
                            <span class="text-sm font-semibold text-on-surface" x-text="vid.quality"></span>
                            <span class="text-xs text-on-surface-variant ml-2">.mp4</span>
                        </div>
                        <span class="material-symbols-outlined text-outline group-hover:text-primary transition-colors">open_in_new</span>
                    </a>
                </template>
            </div>
        </div>

        {{-- Modal Footer Actions --}}
        <div class="p-6 md:p-8 pt-4 border-t border-outline-variant/10 bg-surface-container/30 flex justify-end gap-4 items-center">
            <button @click="showPrivateModal = false; resetModal();" class="px-6 py-2.5 text-on-surface-variant font-medium hover:text-on-surface transition-colors focus:outline-none rounded-lg">
                Batal
            </button>
            <button @click="extractVideo()" 
                    :disabled="isExtracting || sourceCode.trim() === ''"
                    :class="(isExtracting || sourceCode.trim() === '') ? 'opacity-60 cursor-not-allowed' : 'hover:scale-105'"
                    class="px-6 py-2.5 rounded-lg gradient-btn font-semibold flex items-center justify-center min-w-[160px] gap-2 shadow-[0_4px_20px_rgba(203,166,247,0.15)] focus:outline-none transition-all duration-300">
                
                {{-- Extracting Spinner --}}
                <svg x-show="isExtracting" class="animate-spin h-5 w-5 text-on-primary-container" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                
                {{-- Default Icon --}}
                <span x-show="!isExtracting" class="material-symbols-outlined text-lg">downloading</span>
                
                <span x-text="isExtracting ? 'Mengekstrak...' : 'Ekstrak Video'">Ekstrak Video</span>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function privateModalApp() {
    return {
        showPrivateModal: false,
        sourceCode: '',
        isExtracting: false,
        extractError: '',
        extractedVideos: [],
        modalPlatform: 'facebook',

        openModal(detail) {
            this.showPrivateModal = true;
            this.modalPlatform = detail?.platform || 'facebook';
            this.extractError = '';
            this.extractedVideos = [];
        },

        resetModal() {
            this.sourceCode = '';
            this.extractError = '';
            this.extractedVideos = [];
        },

        async extractVideo() {
            if (!this.sourceCode.trim()) return;

            this.isExtracting = true;
            this.extractError = '';
            this.extractedVideos = [];

            try {
                const response = await fetch('/video/extract-private', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        html: this.sourceCode,
                        platform: this.modalPlatform,
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    this.extractedVideos = data.videos;
                } else {
                    this.extractError = data.message || 'Tidak ditemukan link video.';
                }
            } catch (err) {
                this.extractError = 'Gagal terhubung ke server.';
                console.error('Extract error:', err);
            } finally {
                this.isExtracting = false;
            }
        },
    };
}
</script>
@endpush
