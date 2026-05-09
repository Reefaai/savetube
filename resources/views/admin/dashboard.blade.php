@extends('layouts.admin')

@section('title', 'Overview - SaveTube Admin')
@section('header_title', 'Dashboard Overview')

@section('content')
<!-- Metrics Row -->
<section class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-12">
    <!-- Metric 1: Total Pengguna -->
    <div class="bg-surface-variant/60 backdrop-blur-xl border border-outline-variant/15 rounded-2xl p-4 md:p-6 relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300 ease-out shadow-sm">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary/10 rounded-full blur-2xl group-hover:bg-primary/20 transition-colors"></div>
        <div class="flex items-start justify-between mb-4">
            <span class="text-xs md:text-sm font-medium text-on-surface-variant tracking-tight">Total Pengguna</span>
            <span class="material-symbols-outlined text-primary-container bg-primary-container/10 p-1.5 md:p-2 rounded-lg text-sm md:text-base">group</span>
        </div>
        <div class="text-4xl font-black text-on-surface tracking-tighter">
            @if($totalUsers >= 1000)
                {{ number_format($totalUsers / 1000, 1) }}k
            @else
                {{ $totalUsers }}
            @endif
        </div>
    </div>
    <!-- Metric 2: Unduhan Hari Ini -->
    <div class="bg-surface-variant/60 backdrop-blur-xl border border-outline-variant/15 rounded-2xl p-4 md:p-6 relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300 ease-out shadow-sm">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-tertiary/10 rounded-full blur-2xl group-hover:bg-tertiary/20 transition-colors"></div>
        <div class="flex items-start justify-between mb-4">
            <span class="text-xs md:text-sm font-medium text-on-surface-variant tracking-tight">Unduhan Hari Ini</span>
            <span class="material-symbols-outlined text-tertiary-container bg-tertiary-container/10 p-1.5 md:p-2 rounded-lg text-sm md:text-base">download</span>
        </div>
        <div class="text-4xl font-black text-on-surface tracking-tighter">
            @if($downloadsToday >= 1000)
                {{ number_format($downloadsToday / 1000, 1) }}k
            @else
                {{ $downloadsToday }}
            @endif
        </div>
    </div>
    <!-- Metric 3: Total Unduhan Bulan Ini -->
    <div class="bg-surface-variant/60 backdrop-blur-xl border border-outline-variant/15 rounded-2xl p-4 md:p-6 relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300 ease-out shadow-sm">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-secondary/10 rounded-full blur-2xl group-hover:bg-secondary/20 transition-colors"></div>
        <div class="flex items-start justify-between mb-4">
            <span class="text-xs md:text-sm font-medium text-on-surface-variant tracking-tight">Total Unduhan Bulan Ini</span>
            <span class="material-symbols-outlined text-secondary bg-secondary/10 p-1.5 md:p-2 rounded-lg text-sm md:text-base">bar_chart</span>
        </div>
        <div class="text-4xl font-black text-on-surface tracking-tighter">
            @if($downloadsThisMonth >= 1000)
                {{ number_format($downloadsThisMonth / 1000, 1) }}k
            @else
                {{ $downloadsThisMonth }}
            @endif
        </div>
    </div>
    <!-- Metric 4: Rasio Keberhasilan -->
    <div class="bg-surface-variant/60 backdrop-blur-xl border border-outline-variant/15 rounded-2xl p-4 md:p-6 relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300 ease-out shadow-sm">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-tertiary-fixed/10 rounded-full blur-2xl group-hover:bg-tertiary-fixed/20 transition-colors"></div>
        <div class="flex items-start justify-between mb-4">
            <span class="text-xs md:text-sm font-medium text-on-surface-variant tracking-tight">Rasio Keberhasilan</span>
            <span class="material-symbols-outlined text-tertiary bg-tertiary/10 p-1.5 md:p-2 rounded-lg text-sm md:text-base">check_circle</span>
        </div>
        <div class="text-4xl font-black text-on-surface tracking-tighter">{{ $successRate }}%</div>
    </div>
</section>

<!-- Middle Row: Visualization & System Health -->
<section class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
    <!-- Platform Distribution Chart (Dynamic) -->
    <div class="lg:col-span-2 bg-surface-container-low border border-outline-variant/15 rounded-2xl p-6 md:p-8 relative shadow-sm">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-lg font-semibold tracking-tight text-on-surface">Distribusi Platform</h3>
            <span class="text-sm font-medium text-on-surface-variant">Total: {{ $platformStats->sum('total') }}</span>
        </div>

        @if($platformStats->count() > 0)
        @php
            $maxVal = $platformStats->max('total');
            $colors = [
                'youtube'   => 'from-primary-container/20 to-primary-container',
                'tiktok'    => 'from-secondary/20 to-secondary',
                'facebook'  => 'from-tertiary/20 to-tertiary',
                'instagram' => 'from-error/20 to-error',
            ];
            $icons = [
                'youtube'   => 'smart_display',
                'tiktok'    => 'music_note',
                'facebook'  => 'thumb_up',
                'instagram' => 'photo_camera',
            ];
        @endphp
        <!-- Bar Chart Area -->
        <div class="h-64 flex items-end justify-around gap-2 md:gap-4 pt-8 border-b border-outline-variant/20 relative">
            <!-- Y-axis labels -->
            <div class="absolute left-0 top-0 h-full flex flex-col justify-between text-[10px] md:text-xs font-mono text-on-surface-variant/50 pb-8">
                <span>{{ number_format($maxVal) }}</span>
                <span>{{ number_format($maxVal * 0.75) }}</span>
                <span>{{ number_format($maxVal * 0.5) }}</span>
                <span>{{ number_format($maxVal * 0.25) }}</span>
                <span>0</span>
            </div>

            @foreach($platformStats as $stat)
            @php
                $heightPct = $maxVal > 0 ? round(($stat->total / $maxVal) * 80) : 0;
                $gradient  = $colors[$stat->platform_name] ?? 'from-outline/20 to-outline';
                $label     = ucfirst($stat->platform_name);
            @endphp
            <div class="w-16 md:w-24 bg-gradient-to-t {{ $gradient }} rounded-t-lg relative group transition-all duration-500" style="height: {{ $heightPct }}%;">
                <div class="absolute -top-8 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity bg-surface-container-highest px-2 py-1 rounded text-xs font-mono border border-outline-variant/30">{{ number_format($stat->total) }}</div>
                <div class="absolute -bottom-8 left-1/2 -translate-x-1/2 text-xs md:text-sm font-medium text-on-surface-variant whitespace-nowrap">{{ $label }}</div>
            </div>
            @endforeach
        </div>
        @else
        <div class="h-64 flex items-center justify-center text-on-surface-variant/50">
            <div class="text-center">
                <span class="material-symbols-outlined text-4xl mb-2 block">bar_chart</span>
                <p class="text-sm">Belum ada data unduhan.</p>
            </div>
        </div>
        @endif
    </div>

    <!-- System Health Widget -->
    <div class="bg-surface-container-highest border border-outline-variant/15 rounded-2xl p-6 md:p-8 flex flex-col justify-between shadow-[0_20px_40px_rgba(0,0,0,0.2)] relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full blur-3xl"></div>
        <div>
            <h3 class="text-lg font-semibold tracking-tight text-on-surface mb-6">System Health</h3>
            <div class="bg-surface-container-low p-4 rounded-xl border border-outline-variant/10 mb-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-on-surface-variant text-xl">terminal</span>
                    <span class="text-sm font-medium text-on-surface">Eksekutor yt-dlp</span>
                </div>
                <div class="flex items-center gap-2 bg-tertiary/10 px-3 py-1 rounded-full border border-tertiary/20">
                    <span class="w-2 h-2 rounded-full bg-tertiary animate-pulse"></span>
                    <span class="text-xs font-mono text-tertiary">Operasional</span>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="space-y-3 mb-6">
                <div class="bg-surface-container-low p-3 rounded-xl border border-outline-variant/10 flex items-center justify-between">
                    <span class="text-xs text-on-surface-variant">Total Unduhan</span>
                    <span class="text-sm font-mono font-bold text-on-surface">{{ number_format($platformStats->sum('total')) }}</span>
                </div>
                <div class="bg-surface-container-low p-3 rounded-xl border border-outline-variant/10 flex items-center justify-between">
                    <span class="text-xs text-on-surface-variant">Sukses Rate</span>
                    <span class="text-sm font-mono font-bold text-tertiary">{{ $successRate }}%</span>
                </div>
            </div>
        </div>
        <div>
            <div class="flex justify-between items-end mb-2">
                <span class="text-sm font-medium text-on-surface-variant">Rasio Keberhasilan</span>
                <span class="text-lg font-mono font-bold text-on-surface">{{ $successRate }}%</span>
            </div>
            <div class="w-full h-3 bg-surface-container-lowest rounded-full overflow-hidden border border-outline-variant/10">
                <div class="h-full bg-gradient-to-r from-primary to-primary-container rounded-full transition-all duration-700" style="width: {{ $successRate }}%;"></div>
            </div>
        </div>
    </div>
</section>

<!-- Activity Table — Dynamic -->
<x-admin.table title="Aktivitas Terbaru">
    <x-slot name="head">
        <tr class="text-xs font-mono text-on-surface-variant/70 border-b border-outline-variant/10 bg-surface-container-lowest/30">
            <th class="px-6 md:px-8 py-4 font-normal whitespace-nowrap">ID</th>
            <th class="px-6 md:px-8 py-4 font-normal whitespace-nowrap">Waktu</th>
            <th class="px-6 md:px-8 py-4 font-normal whitespace-nowrap">Pengguna</th>
            <th class="px-6 md:px-8 py-4 font-normal whitespace-nowrap">Platform</th>
            <th class="px-6 md:px-8 py-4 font-normal whitespace-nowrap">URL Asli</th>
            <th class="px-6 md:px-8 py-4 font-normal text-right whitespace-nowrap">Status</th>
        </tr>
    </x-slot>

    @forelse($recentActivities as $activity)
    <tr class="border-b border-outline-variant/5 hover:bg-surface-container-highest/30 transition-colors group">
        <td class="px-6 md:px-8 py-4 font-mono text-xs text-primary-container">#DL-{{ str_pad($activity->id, 4, '0', STR_PAD_LEFT) }}</td>
        <td class="px-6 md:px-8 py-4 text-on-surface-variant font-mono text-xs whitespace-nowrap">{{ $activity->created_at->format('H:i A') }}</td>
        <td class="px-6 md:px-8 py-4 font-medium text-on-surface whitespace-nowrap">
            {{ $activity->user ? $activity->user->name : 'Guest' }}
        </td>
        <td class="px-6 md:px-8 py-4 whitespace-nowrap">
            @php
                $pIcon = match($activity->platform_name) {
                    'youtube'   => 'smart_display',
                    'tiktok'    => 'music_note',
                    'facebook'  => 'thumb_up',
                    'instagram' => 'photo_camera',
                    default     => 'link',
                };
            @endphp
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-surface-container-highest border border-outline-variant/20 text-xs font-medium text-on-surface-variant">
                <span class="material-symbols-outlined text-[14px]">{{ $pIcon }}</span> {{ ucfirst($activity->platform_name) }}
            </span>
        </td>
        <td class="px-6 md:px-8 py-4 font-mono text-xs text-on-surface-variant/60 truncate max-w-[200px]" title="{{ $activity->original_url }}">
            {{ Str::limit($activity->original_url, 35) }}
        </td>
        <td class="px-6 md:px-8 py-4 text-right whitespace-nowrap">
            @if($activity->status === 'sukses')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-tertiary/10 border border-tertiary/20 text-xs font-mono text-tertiary">Sukses</span>
            @else
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-error/10 border border-error/20 text-xs font-mono text-error">Gagal</span>
            @endif
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="6" class="px-6 md:px-8 py-12 text-center text-on-surface-variant/50">
            <span class="material-symbols-outlined text-3xl mb-2 block">inbox</span>
            <p class="text-sm">Belum ada aktivitas unduhan.</p>
        </td>
    </tr>
    @endforelse
</x-admin.table>
@endsection
