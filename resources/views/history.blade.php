@extends('layouts.app')

@section('title', 'SaveTube - Riwayat Download')

@section('content')

<div x-data="historyApp()" x-init="init()" x-cloak class="relative z-10 w-full">

    {{-- Page Header --}}
    <section class="flex flex-col gap-4">
        <h1 class="text-4xl md:text-[3.5rem] font-black tracking-[-0.02em] leading-tight bg-clip-text text-transparent bg-gradient-to-r from-primary via-secondary to-tertiary">Riwayat Download</h1>
        <p class="text-xl text-on-surface-variant font-light">Daftar video yang pernah kamu unduh.</p>
    </section>

    {{-- Filters & Search --}}
    <section class="flex flex-col md:flex-row gap-4 p-6 bg-surface-container-low rounded-2xl ghost-border items-center justify-between mt-12">
        <div class="relative w-full md:flex-1">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">search</span>
            <input 
                class="w-full bg-surface-container-lowest border border-outline-variant/15 text-on-surface rounded-xl py-3 pl-12 pr-4 focus:outline-none focus:border-outline-variant/50 focus:ring-2 focus:ring-surface-tint/20 transition-all placeholder:text-outline" 
                placeholder="Cari riwayat..." 
                type="text"
                x-model="searchQuery"
                @input.debounce.400ms="fetchHistory()"
            />
        </div>
        
        <div class="relative w-full md:w-56" x-data="{ filterOpen: false }" @click.away="filterOpen = false">
            <button 
                @click="filterOpen = !filterOpen"
                class="w-full flex items-center justify-between gap-3 px-5 py-3 bg-surface-container-lowest border border-outline-variant/15 rounded-xl text-on-surface hover:border-outline-variant/50 focus:outline-none focus:ring-2 focus:ring-surface-tint/20 transition-all cursor-pointer">
                <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-outline">filter_list</span>
                    <span x-text="activeFilter === 'Semua' ? 'Filter Platform' : activeFilter" class="text-sm font-medium"></span>
                </span>
                <span class="material-symbols-outlined text-[18px] text-outline transition-transform duration-200" :class="filterOpen ? 'rotate-180' : ''">expand_more</span>
            </button>
            <div 
                x-show="filterOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                class="absolute right-0 mt-2 w-full bg-surface-container border border-outline-variant/20 rounded-xl shadow-2xl z-30 overflow-hidden backdrop-blur-xl"
                style="display: none;">
                <template x-for="option in [
                    { name: 'Semua', icon: 'apps' },
                    { name: 'YouTube', icon: 'play_circle' },
                    { name: 'TikTok', icon: 'music_note' },
                    { name: 'Facebook', icon: 'public' },
                    { name: 'Instagram', icon: 'photo_camera' }
                ]" :key="option.name">
                    <button 
                        @click="activeFilter = option.name; filterOpen = false; fetchHistory();"
                        :class="activeFilter === option.name ? 'bg-surface-tint/10 text-primary' : 'text-on-surface hover:bg-surface-variant/50'"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium transition-colors duration-150 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]" x-text="option.icon"></span>
                        <span x-text="option.name"></span>
                        <span x-show="activeFilter === option.name" class="ml-auto material-symbols-outlined text-[16px] text-primary">check</span>
                    </button>
                </template>
            </div>
        </div>
    </section>

    {{-- History List --}}
    <section class="flex flex-col gap-8 mt-8">

        {{-- Loading State --}}
        <div x-show="isLoading" class="flex flex-col items-center justify-center gap-4 py-16" style="display: none;">
            <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-on-surface-variant text-sm">Memuat riwayat...</span>
        </div>

        {{-- Dynamic History Items --}}
        <template x-for="log in logs" :key="log.id">
            <article class="flex flex-col md:flex-row gap-6 p-5 bg-surface-container-low rounded-2xl ghost-border hover:scale-[1.02] transition-transform duration-300 relative overflow-hidden group">
                {{-- Thumbnail --}}
                <div class="w-full md:w-64 h-40 bg-surface-container-highest rounded-xl relative overflow-hidden shrink-0"
                     :class="!log.thumbnail_url ? 'flex items-center justify-center' : ''">
                    <template x-if="log.thumbnail_url">
                        <img 
                            alt="Video thumbnail" 
                            class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity" 
                            :src="log.thumbnail_url"
                            onerror="this.style.display='none'; this.parentElement.querySelector('.thumb-fallback').style.display='flex';"
                        />
                    </template>
                    <template x-if="!log.thumbnail_url">
                        <div class="w-full h-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-4xl text-outline">image</span>
                        </div>
                    </template>
                    {{-- Fallback placeholder saat thumbnail error --}}
                    <div class="thumb-fallback hidden w-full h-full items-center justify-center absolute inset-0 bg-surface-container-highest">
                        <span class="material-symbols-outlined text-4xl text-outline">broken_image</span>
                    </div>
                    <div x-show="log.duration_string" class="absolute bottom-2 right-2 bg-surface-container/80 backdrop-blur-md px-2 py-1 rounded text-xs jetbrains-mono text-on-surface font-bold" x-text="log.duration_string"></div>
                </div>
                
                {{-- Meta --}}
                <div class="flex flex-col justify-between flex-grow py-2 gap-4">
                    <div class="flex flex-col gap-2">
                        <div class="flex justify-between items-start gap-4">
                            <h3 class="text-2xl font-bold text-on-surface leading-snug line-clamp-2" x-text="log.video_title || 'Untitled'"></h3>
                            <button @click="deleteLog(log.id)" aria-label="Hapus" class="text-outline hover:text-error transition-colors flex-shrink-0">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-on-surface-variant flex-wrap">
                            <span class="flex items-center gap-1 bg-surface-container-high px-2 py-1 rounded-md jetbrains-mono text-xs">
                                <span class="material-symbols-outlined text-[16px]" x-text="log.platform_icon"></span> 
                                <span x-text="log.platform_name" class="capitalize"></span>
                            </span>
                            <span>•</span>
                            <span x-text="formatDate(log.created_at)"></span>
                            <template x-if="log.uploader">
                                <span class="text-outline">
                                    • <span x-text="log.uploader"></span>
                                </span>
                            </template>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        {{-- Format Badge --}}
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="jetbrains-mono text-sm text-secondary bg-surface-container-high px-3 py-1.5 rounded-lg border border-outline-variant/20">
                                .<span x-text="(log.file_extension || 'mp4').toUpperCase()"></span>
                                <template x-if="log.format_quality">
                                    <span> • <span x-text="log.format_quality"></span></span>
                                </template>
                                <template x-if="log.formatted_file_size && log.formatted_file_size !== '-'">
                                    <span class="opacity-60"> • <span x-text="log.formatted_file_size"></span></span>
                                </template>
                            </span>
                        </div>
                        
                        {{-- Re-download Button --}}
                        <template x-if="log.can_redownload">
                            <button 
                                @click="redownload(log)"
                                :disabled="log._redownloading"
                                :class="log._redownloading ? 'opacity-70 cursor-wait' : 'hover:scale-105 hover:shadow-[0_0_10px_rgba(163,224,159,0.2)]'"
                                class="px-5 py-2.5 rounded-lg bg-tertiary text-on-tertiary font-medium transition-all flex items-center gap-2">
                                <template x-if="!log._redownloading">
                                    <span class="material-symbols-outlined text-[18px]">download</span>
                                </template>
                                <template x-if="log._redownloading">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </template>
                                <span x-text="log._redownloading ? 'Memproses...' : 'Download Ulang'"></span>
                            </button>
                        </template>
                        <template x-if="!log.can_redownload">
                            <button class="px-5 py-2.5 rounded-lg bg-surface-variant text-on-surface-variant font-medium cursor-not-allowed flex items-center gap-2" disabled>
                                <span class="material-symbols-outlined text-[18px]">block</span> Tidak Tersedia
                            </button>
                        </template>
                    </div>
                </div>
            </article>
        </template>

        {{-- Empty State --}}
        <div x-show="!isLoading && logs.length === 0" class="flex flex-col items-center justify-center gap-6 py-24 px-6 text-center bg-surface-container-low rounded-2xl ghost-border" style="display: none;">
            <div class="w-32 h-32 rounded-full bg-surface-container-low flex items-center justify-center ghost-border">
                <span class="material-symbols-outlined text-6xl text-outline-variant">history</span>
            </div>
            <div class="flex flex-col gap-2 max-w-md">
                <h2 class="text-2xl font-bold text-on-surface">Belum ada riwayat</h2>
                <p class="text-on-surface-variant">Kamu belum pernah mengunduh video apapun. Mulai jelajahi dan simpan video favoritmu!</p>
            </div>
            <a href="{{ route('home') }}" class="px-8 py-3 rounded-xl bg-gradient-to-br from-primary to-primary-container text-on-primary font-bold hover:scale-105 transition-transform mt-4">Mulai Download</a>
        </div>

        {{-- Pagination --}}
        <div x-show="meta.last_page > 1" class="flex items-center justify-center gap-2 pt-4" style="display: none;">
            <button @click="prevPage()" :disabled="meta.current_page <= 1"
                    :class="meta.current_page <= 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-surface-container'"
                    class="px-4 py-2 rounded-lg ghost-border text-on-surface-variant text-sm transition-colors">
                <span class="material-symbols-outlined text-lg">chevron_left</span>
            </button>
            <span class="text-sm text-on-surface-variant font-mono px-4">
                <span x-text="meta.current_page"></span> / <span x-text="meta.last_page"></span>
            </span>
            <button @click="nextPage()" :disabled="meta.current_page >= meta.last_page"
                    :class="meta.current_page >= meta.last_page ? 'opacity-40 cursor-not-allowed' : 'hover:bg-surface-container'"
                    class="px-4 py-2 rounded-lg ghost-border text-on-surface-variant text-sm transition-colors">
                <span class="material-symbols-outlined text-lg">chevron_right</span>
            </button>
        </div>
    </section>

    {{-- Toast Notification --}}
    <div 
        x-show="toast.visible" 
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-5 py-4 rounded-xl shadow-xl backdrop-blur-md border"
        :class="toast.type === 'error' ? 'bg-error-container/90 border-error/30 text-on-error-container' : 'bg-surface-container/90 border-outline-variant/30 text-on-surface'"
        style="display: none;">
        <span class="material-symbols-outlined text-[20px]" x-text="toast.type === 'error' ? 'error' : 'check_circle'"></span>
        <p class="text-sm font-medium" x-text="toast.message"></p>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-show="deleteModal.visible" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-background/80 backdrop-blur-sm" x-transition.opacity @click="deleteModal.visible = false"></div>
        <div class="bg-surface-container border border-outline-variant/20 p-6 rounded-2xl shadow-2xl relative z-10 w-full max-w-sm mx-4"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <h3 class="text-xl font-bold text-on-surface mb-2 flex items-center gap-2">
                <span class="material-symbols-outlined text-error">warning</span> Konfirmasi
            </h3>
            <p class="text-on-surface-variant mb-6 text-sm">Apakah kamu yakin ingin menghapus riwayat download ini?</p>
            <div class="flex justify-end gap-3">
                <button @click="deleteModal.visible = false" class="px-4 py-2 rounded-lg ghost-border text-on-surface hover:bg-surface-variant transition-colors">Batal</button>
                <button @click="confirmDelete()" class="px-4 py-2 rounded-lg bg-error text-on-error font-medium hover:scale-105 transition-transform">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function historyApp() {
    return {
        logs: [],
        meta: { current_page: 1, last_page: 1, total: 0 },
        activeFilter: 'Semua',
        searchQuery: '',
        isLoading: false,
        toast: { visible: false, message: '', type: 'success', _timer: null },
        deleteModal: { visible: false, id: null },

        init() {
            this.fetchHistory();
        },

        showToast(message, type = 'success') {
            clearTimeout(this.toast._timer);
            this.toast.message = message;
            this.toast.type    = type;
            this.toast.visible = true;
            this.toast._timer  = setTimeout(() => { this.toast.visible = false; }, 4000);
        },

        async fetchHistory(page = 1) {
            this.isLoading = true;

            const params = new URLSearchParams({ page });
            if (this.activeFilter !== 'Semua') params.set('platform', this.activeFilter);
            if (this.searchQuery.trim()) params.set('q', this.searchQuery.trim());

            try {
                const response = await fetch(`/api/history?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                const data = await response.json();

                if (data.success) {
                    // Tambahkan state _redownloading ke tiap log
                    this.logs = data.data.map(log => ({ ...log, _redownloading: false }));
                    this.meta = data.meta;
                }
            } catch (err) {
                console.error('History fetch error:', err);
                this.showToast('Gagal memuat riwayat.', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async redownload(log) {
            if (log._redownloading) return;
            log._redownloading = true;

            try {
                const response = await fetch(`/api/history/${log.id}/redownload`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                const data = await response.json();

                if (data.success && data.download_url) {
                    // Trigger download langsung di browser
                    const a = document.createElement('a');
                    a.href     = data.download_url;
                    a.target   = '_blank';
                    a.rel      = 'noopener';
                    a.download = '';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    this.showToast('Download dimulai!', 'success');
                } else {
                    this.showToast(data.message || 'Gagal memulai download.', 'error');
                }
            } catch (err) {
                console.error('Redownload error:', err);
                this.showToast('Gagal terhubung ke server.', 'error');
            } finally {
                log._redownloading = false;
            }
        },

        deleteLog(id) {
            this.deleteModal.id = id;
            this.deleteModal.visible = true;
        },

        async confirmDelete() {
            const id = this.deleteModal.id;
            this.deleteModal.visible = false;

            try {
                const response = await fetch(`/api/history/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                const data = await response.json();
                if (data.success) {
                    this.logs = this.logs.filter(l => l.id !== id);
                    this.meta.total--;
                    this.showToast('Riwayat berhasil dihapus.', 'success');
                }
            } catch (err) {
                console.error('Delete error:', err);
                this.showToast('Gagal menghapus riwayat.', 'error');
            }
        },

        prevPage() {
            if (this.meta.current_page > 1) this.fetchHistory(this.meta.current_page - 1);
        },

        nextPage() {
            if (this.meta.current_page < this.meta.last_page) this.fetchHistory(this.meta.current_page + 1);
        },

        formatDate(dateStr) {
            if (!dateStr) return '';
            const date = new Date(dateStr);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000);

            if (diff < 60) return 'Baru saja';
            if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
            if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';

            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        },
    };
}
</script>
@endpush
