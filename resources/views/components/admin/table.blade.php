@props(['title' => 'Table'])

<section class="bg-surface-container-low border border-outline-variant/15 rounded-2xl overflow-hidden shadow-sm">
    <div class="px-6 md:px-8 py-5 md:py-6 border-b border-outline-variant/15 flex justify-between items-center bg-surface-container/50">
        <h3 class="text-lg font-semibold tracking-tight text-on-surface">{{ $title }}</h3>
        <button class="text-sm font-medium text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">filter_list</span>
            Filter
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <thead>
                {{ $head ?? '' }}
            </thead>
            <tbody class="text-sm">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</section>
