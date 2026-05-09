@extends('layouts.app')

@section('content')
<div class="w-full relative pt-24 pb-32 snap-start">
{{-- ══════════════════════════════════════════════════════════════
     Alpine.js App State — manages the entire download flow
     ══════════════════════════════════════════════════════════════ --}}
<div x-data="saveTubeApp()" x-cloak>

    {{-- Maintenance Banner --}}
    @if($isMaintenance ?? false)
    <div class="max-w-4xl mx-auto px-6 mb-8">
        <div class="flex items-center gap-4 p-5 rounded-2xl bg-error-container/15 border border-error/20 backdrop-blur-sm relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-error/5 to-transparent pointer-events-none"></div>
            <div class="relative flex items-center gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-error/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-error text-2xl animate-pulse">construction</span>
                </div>
                <div>
                    <h3 class="font-semibold text-error text-base mb-0.5">Mode Pemeliharaan Aktif</h3>
                    <p class="text-sm text-on-surface-variant">Fitur download sementara tidak tersedia. Kami sedang melakukan pemeliharaan sistem. Silakan coba lagi nanti.</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Hero Section --}}
    <section class="max-w-4xl mx-auto px-6 pt-16 pb-12 text-center">
        <h1 class="font-headline font-black text-4xl md:text-[3.5rem] leading-[1.1] tracking-[-0.02em] text-on-surface mb-6">
            Download Video dari <br/> 
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-primary-container">Media Sosial Mana Pun</span>
        </h1>
        <p class="font-body text-lg text-on-surface-variant max-w-2xl mx-auto leading-relaxed">
            Arsipkan momen digital dengan kualitas tak tertandingi. Tidak ada batasan, tidak ada watermark. Hanya konten murni.
        </p>
    </section>

    {{-- Platform selector terintegrasi di dalam omni-input --}}

    {{-- Omni-Input Bar --}}
    <x-omni-input />


    {{-- Error Alert --}}
    <div x-show="errorMessage" x-transition.opacity.duration.300ms class="max-w-3xl mx-auto px-6 mb-8" style="display: none;">
        <div class="flex items-center gap-3 p-4 rounded-xl bg-error-container/20 border border-error/20">
            <span class="material-symbols-outlined text-error">error</span>
            <p class="text-sm text-error font-medium" x-text="errorMessage"></p>
            <button @click="errorMessage = ''" class="ml-auto text-error/60 hover:text-error transition-colors">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
    </div>

    {{-- Result Card (Dynamic) --}}
    <div x-show="showResult" x-transition:enter="ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
        <section class="max-w-4xl mx-auto px-6 relative z-20">
            <div class="flex flex-col md:flex-row gap-8 bg-surface-container-low rounded-2xl p-6 ambient-shadow ghost-border relative overflow-hidden card-scale">
                {{-- Decorative --}}
                <div class="absolute -right-32 -top-32 w-64 h-64 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>

                {{-- Thumbnail --}}
                <div class="w-full md:w-2/5 aspect-video rounded-xl overflow-hidden relative shadow-lg bg-surface flex-shrink-0 z-10">
                    <img :alt="video.title" class="w-full h-full object-cover"
                         :src="video.thumbnail || 'https://placehold.co/640x360/1e1e2e/cba6f7?text=No+Thumbnail'"
                         onerror="this.onerror=null; this.src='https://placehold.co/640x360/1e1e2e/cba6f7?text=Tidak+Ada+Thumbnail';" />
                    <div class="absolute bottom-3 right-3 bg-[#11111b]/80 backdrop-blur-md px-2 py-1 rounded text-xs font-mono text-on-surface z-20" x-text="video.duration_str"></div>
                </div>

                {{-- Meta & Actions --}}
                <div class="w-full md:w-3/5 flex flex-col justify-between z-10 py-2">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <span class="bg-surface-container-high px-3 py-1 rounded-full text-xs font-mono text-primary-container tracking-wider border border-outline-variant/30 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">play_circle</span>
                                <span x-text="platform" class="capitalize"></span>
                            </span>
                            <span class="font-mono text-xs text-outline">.MP4</span>
                        </div>
                        <h3 class="font-headline font-bold text-2xl text-on-surface mb-2 leading-tight" x-text="video.title"></h3>
                        <p class="font-body text-sm text-on-surface-variant line-clamp-2">
                            <span class="text-outline">oleh</span> <span x-text="video.uploader"></span>
                        </p>
                    </div>

                    {{-- Download Buttons: Video Formats (max 3) --}}
                    <div class="mt-8 flex flex-wrap gap-3">
                        <template x-for="(fmt, i) in videoFormats" :key="fmt.format_id">
                            <a :href="buildDownloadUrl(fmt)"
                               @click="logDownload(fmt)"
                               target="_blank" rel="noopener"
                               :class="{
                                   'bg-tertiary text-on-tertiary shadow-[0_0_10px_rgba(163,224,159,0.2)]': i === 0,
                                   'bg-secondary text-on-secondary': i === 1,
                                   'bg-surface-container-high text-on-surface ghost-border': i >= 2,
                               }"
                               class="font-semibold px-5 py-2.5 rounded-lg flex items-center gap-2 hover-scale text-sm">
                                <span class="material-symbols-outlined text-[20px]" x-text="i === 0 ? 'high_quality' : (i === 1 ? 'sd' : 'video_file')"></span>
                                <span x-text="fmt.quality"></span>
                                <span x-show="fmt.filesize" class="text-[10px] opacity-70" x-text="formatBytes(fmt.filesize)"></span>
                            </a>
                        </template>

                        {{-- Audio Only — Selalu tampil jika ada --}}
                        <template x-if="audioFormat">
                            <a :href="buildDownloadUrl(audioFormat)"
                               @click="logDownload(audioFormat)"
                               target="_blank" rel="noopener"
                               class="audio-gradient font-semibold px-5 py-2.5 rounded-lg flex items-center gap-2 hover-scale text-sm">
                                <span class="material-symbols-outlined text-[20px]">music_note</span>
                                <span>Audio Only</span>
                                <span x-show="audioFormat.filesize" class="text-[10px] opacity-70" x-text="formatBytes(audioFormat.filesize)"></span>
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </section>

        {{-- Guest Login Hint --}}
        @guest
        <div class="text-center mt-6">
            <p class="text-xs text-outline">
                <a href="{{ route('login') }}" class="text-primary hover:text-primary-container underline underline-offset-2 transition-colors">Login</a>
                untuk menyimpan riwayat download kamu.
            </p>
        </div>
        @endguest
    </div>


</div>
</div>

<div class="w-full bg-surface-container-low border-t border-outline-variant/10 relative mt-12">
    {{-- Gradient separator line --}}
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-3/4 max-w-4xl h-px bg-gradient-to-r from-transparent via-primary/30 to-transparent"></div>
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-1/2 max-w-xl h-[2px] bg-gradient-to-r from-transparent via-primary/40 to-transparent blur-sm"></div>

{{-- ══════════════════════════════════════════════════════════════
     Wiki / Informational Sections
     ══════════════════════════════════════════════════════════════ --}}
<section class="max-w-6xl mx-auto px-6 py-16 space-y-28 relative z-20">

    {{-- ── Section 1: Platform yang Didukung ────────────────────── --}}
    <div class="text-center snap-start">
        <h2 class="font-headline font-bold text-2xl md:text-3xl text-on-surface tracking-tight mb-3">Platform yang Didukung</h2>
        <p class="text-on-surface-variant text-sm max-w-xl mx-auto mb-10">Download video dari platform favoritmu — tanpa ribet, tanpa batas.</p>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-3xl mx-auto">
            {{-- YouTube --}}
            <div class="group relative p-6 rounded-2xl bg-surface-container-high ghost-border hover:bg-error/5 hover:border-error/20 transition-all duration-300 cursor-default">
                <div class="w-12 h-12 rounded-xl bg-error/10 flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                    <span class="material-symbols-outlined text-error text-2xl">play_circle</span>
                </div>
                <span class="block font-semibold text-sm text-on-surface">YouTube</span>
                <span class="block text-[11px] text-outline mt-1">Video & Shorts</span>
            </div>
            {{-- TikTok --}}
            <div class="group relative p-6 rounded-2xl bg-surface-container-high ghost-border hover:bg-[#89dceb]/5 hover:border-[#89dceb]/20 transition-all duration-300 cursor-default">
                <div class="w-12 h-12 rounded-xl bg-[#89dceb]/10 flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                    <span class="material-symbols-outlined text-[#89dceb] text-2xl">music_note</span>
                </div>
                <span class="block font-semibold text-sm text-on-surface">TikTok</span>
                <span class="block text-[11px] text-outline mt-1">Tanpa Watermark</span>
            </div>
            {{-- Facebook --}}
            <div class="group relative p-6 rounded-2xl bg-surface-container-high ghost-border hover:bg-[#89b4fa]/5 hover:border-[#89b4fa]/20 transition-all duration-300 cursor-default">
                <div class="w-12 h-12 rounded-xl bg-[#89b4fa]/10 flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                    <span class="material-symbols-outlined text-[#89b4fa] text-2xl">thumb_up</span>
                </div>
                <span class="block font-semibold text-sm text-on-surface">Facebook</span>
                <span class="block text-[11px] text-outline mt-1">Video & Reels</span>
            </div>
            {{-- Instagram --}}
            <div class="group relative p-6 rounded-2xl bg-surface-container-high ghost-border hover:bg-[#f5c2e7]/5 hover:border-[#f5c2e7]/20 transition-all duration-300 cursor-default">
                <div class="w-12 h-12 rounded-xl bg-[#f5c2e7]/10 flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                    <span class="material-symbols-outlined text-[#f5c2e7] text-2xl">photo_camera</span>
                </div>
                <span class="block font-semibold text-sm text-on-surface">Instagram</span>
                <span class="block text-[11px] text-outline mt-1">Reels & Stories</span>
            </div>
        </div>
    </div>

    {{-- ── Section 2: Cara Download Video ───────────────────────── --}}
    <div class="text-center snap-start">
        <h2 class="font-headline font-bold text-2xl md:text-3xl text-on-surface tracking-tight mb-3">Cara Download Video</h2>
        <p class="text-on-surface-variant text-sm max-w-xl mx-auto mb-12">Tiga langkah simpel — kontenmu tersimpan dalam hitungan detik.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
            {{-- Step 1 --}}
            <div class="relative p-8 rounded-2xl bg-surface-container-low ghost-border group hover:-translate-y-1 transition-all duration-300">
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 w-8 h-8 rounded-full bg-gradient-to-br from-primary to-primary-container flex items-center justify-center text-on-primary font-bold text-sm shadow-lg">1</div>
                <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center mx-auto mb-4 mt-2 group-hover:bg-primary/15 transition-colors">
                    <span class="material-symbols-outlined text-primary text-3xl">content_copy</span>
                </div>
                <h3 class="font-headline font-bold text-on-surface mb-2">Salin Tautan</h3>
                <p class="text-sm text-on-surface-variant leading-relaxed">Cari video yang ingin kamu simpan di platform media sosial favorit, lalu salin URL-nya.</p>
            </div>
            {{-- Step 2 --}}
            <div class="relative p-8 rounded-2xl bg-surface-container-low ghost-border group hover:-translate-y-1 transition-all duration-300">
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 w-8 h-8 rounded-full bg-gradient-to-br from-secondary to-secondary-container flex items-center justify-center text-on-secondary font-bold text-sm shadow-lg">2</div>
                <div class="w-14 h-14 rounded-2xl bg-secondary/10 flex items-center justify-center mx-auto mb-4 mt-2 group-hover:bg-secondary/15 transition-colors">
                    <span class="material-symbols-outlined text-secondary text-3xl">content_paste_go</span>
                </div>
                <h3 class="font-headline font-bold text-on-surface mb-2">Tempel & Proses</h3>
                <p class="text-sm text-on-surface-variant leading-relaxed">Tempelkan URL ke kolom input di atas. Platform akan terdeteksi otomatis, lalu klik Proses.</p>
            </div>
            {{-- Step 3 --}}
            <div class="relative p-8 rounded-2xl bg-surface-container-low ghost-border group hover:-translate-y-1 transition-all duration-300">
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 w-8 h-8 rounded-full bg-gradient-to-br from-tertiary to-tertiary-container flex items-center justify-center text-on-tertiary font-bold text-sm shadow-lg">3</div>
                <div class="w-14 h-14 rounded-2xl bg-tertiary/10 flex items-center justify-center mx-auto mb-4 mt-2 group-hover:bg-tertiary/15 transition-colors">
                    <span class="material-symbols-outlined text-tertiary text-3xl">download</span>
                </div>
                <h3 class="font-headline font-bold text-on-surface mb-2">Unduh Selesai</h3>
                <p class="text-sm text-on-surface-variant leading-relaxed">Pilih kualitas video (HD/SD) atau format audio, lalu unduh langsung ke perangkatmu.</p>
            </div>
        </div>
    </div>

    {{-- ── Section 3: Fitur Unggulan ────────────────────────────── --}}
    <div class="text-center snap-start">
        <h2 class="font-headline font-bold text-2xl md:text-3xl text-on-surface tracking-tight mb-3">Fitur Unggulan</h2>
        <p class="text-on-surface-variant text-sm max-w-xl mx-auto mb-10">Dirancang untuk kecepatan, kualitas, dan privasi tanpa kompromi.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 max-w-4xl mx-auto">
            {{-- Feature 1 --}}
            <div class="p-7 rounded-2xl bg-surface-container ghost-border text-left group hover:bg-surface-container-high transition-all duration-300 relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-24 h-24 bg-primary/5 rounded-full blur-2xl pointer-events-none group-hover:bg-primary/10 transition-colors"></div>
                <div class="relative z-10">
                    <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-primary text-xl">high_quality</span>
                    </div>
                    <h3 class="font-headline font-bold text-on-surface mb-1.5">Kualitas Maksimal</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">Unduh video hingga resolusi 4K. Kami mengambil file asli langsung dari sumbernya tanpa kompresi tambahan.</p>
                </div>
            </div>
            {{-- Feature 2 --}}
            <div class="p-7 rounded-2xl bg-surface-container ghost-border text-left group hover:bg-surface-container-high transition-all duration-300 relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-24 h-24 bg-tertiary/5 rounded-full blur-2xl pointer-events-none group-hover:bg-tertiary/10 transition-colors"></div>
                <div class="relative z-10">
                    <div class="w-11 h-11 rounded-xl bg-tertiary/10 flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-tertiary text-xl">water_drop</span>
                    </div>
                    <h3 class="font-headline font-bold text-on-surface mb-1.5">Bebas Watermark</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">Dapatkan konten bersih dari watermark. Kami mengekstrak file master, bukan salinan yang sudah diproses ulang.</p>
                </div>
            </div>
            {{-- Feature 3 --}}
            <div class="p-7 rounded-2xl bg-surface-container ghost-border text-left group hover:bg-surface-container-high transition-all duration-300 relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-24 h-24 bg-secondary/5 rounded-full blur-2xl pointer-events-none group-hover:bg-secondary/10 transition-colors"></div>
                <div class="relative z-10">
                    <div class="w-11 h-11 rounded-xl bg-secondary/10 flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-secondary text-xl">bolt</span>
                    </div>
                    <h3 class="font-headline font-bold text-on-surface mb-1.5">Super Cepat</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">Infrastruktur server kecepatan tinggi. Proses ekstraksi dan pengunduhan berlangsung dalam hitungan detik.</p>
                </div>
            </div>
            {{-- Feature 4 --}}
            <div class="p-7 rounded-2xl bg-surface-container ghost-border text-left group hover:bg-surface-container-high transition-all duration-300 relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-24 h-24 bg-error/5 rounded-full blur-2xl pointer-events-none group-hover:bg-error/10 transition-colors"></div>
                <div class="relative z-10">
                    <div class="w-11 h-11 rounded-xl bg-primary-container/10 flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-primary-container text-xl">lock</span>
                    </div>
                    <h3 class="font-headline font-bold text-on-surface mb-1.5">Privasi Terjamin</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">Kami tidak menyimpan log unduhan atau melacak aktivitasmu. Sesi berakhir saat kamu menutup tab.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Section 4: FAQ ───────────────────────────────────────── --}}
    <div class="max-w-3xl mx-auto snap-start">
        <div class="text-center mb-10">
            <h2 class="font-headline font-bold text-2xl md:text-3xl text-on-surface tracking-tight mb-3">Pertanyaan Umum</h2>
            <p class="text-on-surface-variant text-sm">Jawaban cepat untuk pertanyaan yang sering ditanyakan.</p>
        </div>

        <div class="space-y-3" x-data="{ openFaq: null }">
            {{-- FAQ 1 --}}
            <details class="group rounded-xl ghost-border bg-surface-container overflow-hidden" @toggle="openFaq = $el.open ? 1 : null">
                <summary class="flex items-center justify-between p-5 cursor-pointer select-none hover:bg-surface-container-high transition-colors">
                    <span class="font-semibold text-sm text-on-surface">Apakah layanan ini sepenuhnya gratis?</span>
                    <span class="material-symbols-outlined text-outline text-lg transition-transform duration-300 group-open:rotate-180">expand_more</span>
                </summary>
                <div class="px-5 pb-5 text-sm text-on-surface-variant leading-relaxed">
                    Ya, 100% gratis. Tidak ada biaya tersembunyi, langganan, atau batasan jumlah unduhan. Kamu bisa mengunduh sebanyak yang kamu mau tanpa perlu membuat akun.
                </div>
            </details>
            {{-- FAQ 2 --}}
            <details class="group rounded-xl ghost-border bg-surface-container overflow-hidden">
                <summary class="flex items-center justify-between p-5 cursor-pointer select-none hover:bg-surface-container-high transition-colors">
                    <span class="font-semibold text-sm text-on-surface">Format file apa saja yang didukung?</span>
                    <span class="material-symbols-outlined text-outline text-lg transition-transform duration-300 group-open:rotate-180">expand_more</span>
                </summary>
                <div class="px-5 pb-5 text-sm text-on-surface-variant leading-relaxed">
                    SaveTube mendukung format video MP4 (hingga 4K/1080p/720p) dan audio MP3. Format yang tersedia bergantung pada kualitas file asli di platform sumbernya.
                </div>
            </details>
            {{-- FAQ 3 --}}
            <details class="group rounded-xl ghost-border bg-surface-container overflow-hidden">
                <summary class="flex items-center justify-between p-5 cursor-pointer select-none hover:bg-surface-container-high transition-colors">
                    <span class="font-semibold text-sm text-on-surface">Mengapa beberapa video tidak bisa diunduh?</span>
                    <span class="material-symbols-outlined text-outline text-lg transition-transform duration-300 group-open:rotate-180">expand_more</span>
                </summary>
                <div class="px-5 pb-5 text-sm text-on-surface-variant leading-relaxed">
                    Video yang bersifat privat, dibatasi usianya, atau dilindungi secara regional mungkin tidak dapat diunduh. Pastikan video tersebut bersifat publik dan gunakan URL langsung dari platform sumbernya.
                </div>
            </details>
            {{-- FAQ 4 --}}
            <details class="group rounded-xl ghost-border bg-surface-container overflow-hidden">
                <summary class="flex items-center justify-between p-5 cursor-pointer select-none hover:bg-surface-container-high transition-colors">
                    <span class="font-semibold text-sm text-on-surface">Apakah aman menggunakan SaveTube?</span>
                    <span class="material-symbols-outlined text-outline text-lg transition-transform duration-300 group-open:rotate-180">expand_more</span>
                </summary>
                <div class="px-5 pb-5 text-sm text-on-surface-variant leading-relaxed">
                    Sangat aman. Kami tidak menyimpan data pribadimu, tidak melacak unduhan, dan tidak menyisipkan skrip berbahaya. Koneksi dilindungi enkripsi SSL standar industri.
                </div>
            </details>
        </div>
    </div>

    {{-- ── Section 5: Untuk Developer ───────────────────────────── --}}
    <div class="max-w-3xl mx-auto snap-start">
        <div class="text-center mb-10">
            <h2 class="font-headline font-bold text-2xl md:text-3xl text-on-surface tracking-tight mb-3">Untuk Developer</h2>
            <p class="text-on-surface-variant text-sm">Integrasikan SaveTube ke dalam proyek kamu dengan API sederhana.</p>
        </div>

        <div class="rounded-2xl overflow-hidden ghost-border bg-surface-container-lowest">
            <div class="flex items-center gap-2 px-5 py-3 bg-surface-container-lowest border-b border-outline-variant/10">
                <div class="flex gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-[#f38ba8]"></span>
                    <span class="w-3 h-3 rounded-full bg-[#f9e2af]"></span>
                    <span class="w-3 h-3 rounded-full bg-[#a6e3a1]"></span>
                </div>
                <span class="text-xs font-mono text-outline ml-2">api_reference.js</span>
            </div>
            <pre class="p-6 text-sm font-mono text-on-surface-variant leading-relaxed overflow-x-auto"><code><span class="text-secondary">const</span> extractMedia = <span class="text-tertiary">async</span> (url) => {
  <span class="text-secondary">const</span> response = <span class="text-tertiary">await</span> <span class="text-primary-container">fetch</span>(<span class="text-tertiary-fixed-dim">'/video/process'</span>, {
    method: <span class="text-tertiary-fixed-dim">'POST'</span>,
    headers: {
      <span class="text-tertiary-fixed-dim">'Content-Type'</span>: <span class="text-tertiary-fixed-dim">'application/json'</span>,
      <span class="text-tertiary-fixed-dim">'X-CSRF-TOKEN'</span>: csrfToken,
    },
    body: <span class="text-primary-container">JSON</span>.<span class="text-primary-container">stringify</span>({ url, platform: <span class="text-tertiary-fixed-dim">'youtube'</span> }),
  });

  <span class="text-secondary">return</span> response.<span class="text-primary-container">json</span>();
};</code></pre>
        </div>
        <p class="text-center text-xs text-outline mt-4">
            SaveTube menggunakan engine ekstraksi konten melalui proxy dinamis
            untuk memastikan ketersediaan layanan 99.9%.
        </p>
    </div>

</section>
</div>

@push('scripts')
<script>
function saveTubeApp() {
    return {
        // ── State ────────────────────────────────────────────
        videoUrl:         '',
        selectedPlatform: '',   // yang dipilih user (input)
        showPlatformPicker: false,  // dropdown manual platform
        isLoading:        false,
        showResult:       false,
        errorMessage:     '',
        platform:         '',   // yang dikembalikan backend (output, untuk result card)
        video:            {},
        formats:          [],

        // ── Init: auto-detect platform saat URL di-paste ─────
        init() {
            this.$watch('videoUrl', (url) => {
                if (!url) { this.selectedPlatform = ''; this.showPlatformPicker = false; return; }
                const low = url.toLowerCase();
                let detected = '';
                if      (low.includes('youtube.com') || low.includes('youtu.be'))              detected = 'youtube';
                else if (low.includes('tiktok.com'))                                           detected = 'tiktok';
                else if (low.includes('facebook.com') || low.includes('fb.watch') || low.includes('fb.com')) detected = 'facebook';
                else if (low.includes('instagram.com') || low.includes('instagr.am'))          detected = 'instagram';
                if (detected) { this.selectedPlatform = detected; this.showPlatformPicker = false; }
            });
        },

        // ── Computed: pisahkan video dan audio ───────────────
        get videoFormats() {
            return this.formats.filter(f => f.type === 'video').slice(0, 3);
        },
        get audioFormat() {
            return this.formats.find(f => f.type === 'audio') || null;
        },

        // ── Process URL ──────────────────────────────────────
        async processUrl() {
            if (!this.videoUrl.trim() || !this.selectedPlatform) return;

            this.isLoading    = true;
            this.showResult   = false;
            this.errorMessage = '';

            try {
                const response = await fetch('/video/process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        url:      this.videoUrl,
                        platform: this.selectedPlatform,  // kirim platform ke backend
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    this.platform   = data.platform;
                    this.video      = data.video;
                    this.formats    = data.formats;
                    this.showResult = true;

                    setTimeout(() => {
                        window.scrollBy({ top: 350, behavior: 'smooth' });
                    }, 150);
                } else if (data.maintenance) {
                    this.errorMessage = data.message || 'SaveTube sedang dalam mode pemeliharaan. Fitur download sementara tidak tersedia.';
                } else {
                    this.errorMessage = data.message || 'Terjadi kesalahan.';
                }
            } catch (err) {
                // Handle maintenance mode response
                if (err.maintenance) {
                    this.errorMessage = err.message;
                } else {
                    this.errorMessage = 'Gagal terhubung ke server. Periksa koneksi Anda.';
                }
                console.error('SaveTube error:', err);
            } finally {
                this.isLoading = false;
            }
        },

        // ── Log download ke history (fire & forget) ─────────
        logDownload(fmt) {
            @auth
            fetch('/api/history/log', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    original_url:  this.videoUrl || this.video.webpage_url || '',
                    platform_name: this.platform,
                    video_title:   this.video.title,
                    thumbnail_url: this.video.thumbnail || null,
                    duration:      this.video.duration || null,
                    duration_str:  this.video.duration_str || null,
                    uploader:      this.video.uploader || null,
                    format_id:     fmt.format_id,
                    ext:           fmt.ext || 'mp4',
                    quality:       fmt.quality,
                    filesize:      fmt.filesize || null,
                }),
            }).catch(() => {}); // Fire and forget — jangan blokir download
            @endauth
        },

        // ── Bangun URL download ──────────────────────────────
        buildDownloadUrl(fmt) {
            const base        = '/video/download';
            const originalUrl = this.videoUrl || this.video.webpage_url || '';
            const params      = new URLSearchParams({
                original_url: originalUrl,
                format_id:    fmt.format_id,
                url:          fmt.url || '',
                title:        this.video.title || 'video',
                ext:          fmt.ext || 'mp4',
            });
            return `${base}?${params.toString()}`;
        },

        // ── Format bytes ke string terbaca manusia ───────────
        formatBytes(bytes) {
            if (!bytes) return '';
            const units = ['B', 'KB', 'MB', 'GB'];
            let i = 0;
            while (bytes >= 1024 && i < units.length - 1) { bytes /= 1024; i++; }
            return Math.round(bytes * 10) / 10 + ' ' + units[i];
        },
    };
}
</script>
@endpush
@endsection

