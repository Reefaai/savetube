@props([
    'title',
    'platform',
    'platformIcon',
    'time',
    'format',
    'size',
    'thumbnail',
    'duration',
    'deleted' => false,
])

<article class="flex flex-col md:flex-row gap-6 p-4 bg-surface-container rounded-2xl ghost-border hover:scale-[1.02] transition-transform duration-300 relative overflow-hidden group {{ $deleted ? 'opacity-60' : '' }}">
    <div class="w-full md:w-64 h-40 bg-surface-container-highest rounded-xl relative overflow-hidden shrink-0 {{ $deleted ? 'flex items-center justify-center' : '' }}">
        @if($deleted)
            <span class="material-symbols-outlined text-4xl text-outline">broken_image</span>
        @else
            <img alt="Video thumbnail" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity" src="{{ $thumbnail }}"/>
            <div class="absolute bottom-2 right-2 bg-surface-container/80 backdrop-blur-md px-2 py-1 rounded text-xs jetbrains-mono text-on-surface font-bold">{{ $duration }}</div>
        @endif
    </div>
    
    <div class="flex flex-col justify-between flex-grow py-2 gap-4">
        <div class="flex flex-col gap-2">
            <div class="flex justify-between items-start gap-4">
                <h3 class="text-2xl font-bold text-on-surface leading-snug line-clamp-2 {{ $deleted ? 'text-outline' : '' }}">{{ $title }}</h3>
                <button aria-label="Hapus" class="text-outline hover:text-error transition-colors">
                    <span class="material-symbols-outlined">delete</span>
                </button>
            </div>
            <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                <span class="flex items-center gap-1 bg-surface-container-high px-2 py-1 rounded-md jetbrains-mono text-xs">
                    <span class="material-symbols-outlined text-[16px]">{{ $platformIcon }}</span> {{ $platform }}
                </span>
                <span>•</span>
                <span>{{ $time }}</span>
            </div>
        </div>
        
        <div class="flex items-center justify-between">
            <span class="jetbrains-mono text-sm {{ $deleted ? 'text-outline' : ($platform == 'TikTok' ? 'text-[#fab387]' : 'text-secondary') }} bg-surface-container-high px-3 py-1.5 rounded-lg border border-outline-variant/20">{{ $format }} • {{ $size }}</span>
            
            @if($deleted)
                <button class="px-5 py-2.5 rounded-lg bg-surface-variant text-on-surface-variant font-medium cursor-not-allowed flex items-center gap-2" disabled>
                    <span class="material-symbols-outlined text-[18px]">block</span> Tidak Tersedia
                </button>
            @else
                <button class="px-5 py-2.5 rounded-lg {{ $platform == 'TikTok' ? 'bg-gradient-to-br from-[#fab387] to-primary-container text-on-primary' : 'bg-tertiary text-on-tertiary' }} font-medium hover:scale-105 transition-transform {{ $platform != 'TikTok' ? 'hover:shadow-[0_0_10px_rgba(163,224,159,0.2)]' : '' }} flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">download</span> Download Ulang
                </button>
            @endif
        </div>
    </div>
</article>
