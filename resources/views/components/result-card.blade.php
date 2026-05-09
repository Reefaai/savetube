@props([
    'title' => 'Tutorial Laravel Pemula',
    'description' => 'Pelajari dasar-dasar framework PHP modern ini dari awal hingga akhir dengan panduan komprehensif.',
    'platform' => 'YouTube',
    'duration' => '12:34',
    'size' => '45 MB',
    'extension' => '.MP4',
    'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuA174Ff2_p-JHwRCqr220X-rXPe_AyVrd6zkn3zegzFXHBw_FoD5rVPVuhYPIJ_R7ltlD1qPJkdbs0imdopWm9si5_-cvV-IwFzUABFRD0y3CvMZwJN5amWtRuD9o0U37a6chtthB_oxZ1wvnE1TqPxaNcThG3X4g-tLbUAyKN0wu__LszPNrbY_yTPCqekSFytbLQ6dAC5s1yybOoSEStYcrHqJi7C31zGTZmqb9Fpuhu0Wo40h1UHYzO7HxV6EZFLgwkrZbyU5pY'
])

<section class="max-w-4xl mx-auto px-6 relative z-20">
    <div class="flex flex-col md:flex-row gap-8 bg-surface-container-low rounded-2xl p-6 ambient-shadow ghost-border relative overflow-hidden card-scale">
        <!-- Decorative Layer -->
        <div class="absolute -right-32 -top-32 w-64 h-64 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>
        
        <!-- Thumbnail Container -->
        <div class="w-full md:w-2/5 aspect-video rounded-xl overflow-hidden relative shadow-lg bg-surface flex-shrink-0 z-10">
            <img alt="Video Thumbnail" class="w-full h-full object-cover" src="{{ $image }}"/>
            
            <!-- Duration Badge -->
            <div class="absolute bottom-3 right-3 bg-[#11111b]/80 backdrop-blur-md px-2 py-1 rounded text-xs font-mono text-on-surface z-20">
                {{ $duration }}
            </div>
        </div>
        
        <!-- Meta & Actions -->
        <div class="w-full md:w-3/5 flex flex-col justify-between z-10 py-2">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="bg-surface-container-high px-3 py-1 rounded-full text-xs font-mono text-primary-container tracking-wider border border-outline-variant/30 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">play_circle</span>
                        {{ $platform }}
                    </span>
                    <span class="font-mono text-xs text-outline">{{ $extension }} / {{ $size }}</span>
                </div>
                <h3 class="font-headline font-bold text-2xl text-on-surface mb-2 leading-tight">{{ $title }}</h3>
                <p class="font-body text-sm text-on-surface-variant line-clamp-2">
                    {{ $description }}
                </p>
            </div>
            
            <!-- Download Options -->
            @isset($actions)
                <div class="mt-8 flex flex-wrap gap-3">
                    {{ $actions }}
                </div>
            @else
                <div class="mt-8 flex flex-wrap gap-3">
                    <!-- HD Button (Green) -->
                    <button class="bg-tertiary text-on-tertiary font-bold px-5 py-2.5 rounded-lg flex items-center gap-2 hover-scale text-sm shadow-[0_0_10px_rgba(163,224,159,0.2)]">
                        <span class="material-symbols-outlined text-[20px]">high_quality</span>
                        HD 1080p
                    </button>
                    <!-- SD Button (Lavender) -->
                    <button class="bg-secondary text-on-secondary font-semibold px-5 py-2.5 rounded-lg flex items-center gap-2 hover-scale text-sm">
                        <span class="material-symbols-outlined text-[20px]">sd</span>
                        SD 720p
                    </button>
                    <!-- Audio Button (Peach) -->
                    <button class="audio-gradient font-semibold px-5 py-2.5 rounded-lg flex items-center gap-2 hover-scale text-sm">
                        <span class="material-symbols-outlined text-[20px]">headphones</span>
                        Audio MP3
                    </button>
                </div>
            @endisset
        </div>
    </div>
</section>
