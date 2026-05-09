@extends('layouts.admin')

@section('title', 'User Management - SaveTube Admin')
@section('header_title', 'User Management')

@section('content')
<!-- Flash Messages -->
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
     class="mb-6 px-5 py-3 rounded-xl bg-tertiary/10 border border-tertiary/20 text-tertiary text-sm font-medium flex items-center gap-3">
    <span class="material-symbols-outlined text-[18px]">check_circle</span>
    {{ session('success') }}
    <button @click="show = false" class="ml-auto hover:text-on-surface transition-colors">
        <span class="material-symbols-outlined text-[16px]">close</span>
    </button>
</div>
@endif

@if(session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
     class="mb-6 px-5 py-3 rounded-xl bg-error/10 border border-error/20 text-error text-sm font-medium flex items-center gap-3">
    <span class="material-symbols-outlined text-[18px]">error</span>
    {{ session('error') }}
    <button @click="show = false" class="ml-auto hover:text-on-surface transition-colors">
        <span class="material-symbols-outlined text-[16px]">close</span>
    </button>
</div>
@endif

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-on-surface">Manajemen Akses & Pengguna</h1>
        <p class="text-sm text-on-surface-variant font-medium mt-1">
            Kelola data administrator dan pengguna sistem Anda.
            <span class="text-on-surface-variant/50 ml-1">({{ $users->total() }} pengguna)</span>
        </p>
    </div>
</div>

<!-- Table Component -->
<x-admin.table title="Daftar Sistem & Pengguna">
    <x-slot name="head">
        <tr class="text-xs font-mono text-on-surface-variant/70 border-b border-outline-variant/10 bg-surface-container-lowest/30">
            <th class="px-6 md:px-8 py-4 font-normal whitespace-nowrap">ID</th>
            <th class="px-6 md:px-8 py-4 font-normal whitespace-nowrap">Nama</th>
            <th class="px-6 md:px-8 py-4 font-normal whitespace-nowrap">Email</th>
            <th class="px-6 md:px-8 py-4 font-normal whitespace-nowrap">Role</th>
            <th class="px-6 md:px-8 py-4 font-normal whitespace-nowrap">Status</th>
            <th class="px-6 md:px-8 py-4 font-normal whitespace-nowrap text-center">Downloads</th>
            <th class="px-6 md:px-8 py-4 font-normal text-right whitespace-nowrap">Aksi</th>
        </tr>
    </x-slot>

    @forelse($users as $user)
    @php
        $initials = collect(explode(' ', $user->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
        $isSelf   = $user->id === Auth::id();
    @endphp
    <tr class="border-b border-outline-variant/5 hover:bg-surface-container-highest/30 transition-colors group {{ !$user->is_active ? 'opacity-60' : '' }}"
        x-data="{ confirmDelete: false }">
        <td class="px-6 md:px-8 py-4 font-mono text-xs text-primary-container">#USR-{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</td>
        <td class="px-6 md:px-8 py-4 whitespace-nowrap">
            <div class="flex items-center gap-3">
                @if($user->role === 'admin')
                <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-xs ring-1 ring-primary/30">{{ $initials }}</div>
                @else
                <div class="w-8 h-8 rounded-full bg-surface-container-highest flex items-center justify-center text-on-surface-variant font-bold text-xs ring-1 ring-outline-variant/30">{{ $initials }}</div>
                @endif
                <div class="flex flex-col">
                    <span class="font-medium text-on-surface">{{ $user->name }}</span>
                    @if($isSelf)
                    <span class="text-[10px] text-primary font-mono">Anda</span>
                    @endif
                </div>
            </div>
        </td>
        <td class="px-6 md:px-8 py-4 text-sm text-on-surface-variant whitespace-nowrap">{{ $user->email }}</td>
        <td class="px-6 md:px-8 py-4 whitespace-nowrap">
            @if($user->role === 'admin')
            <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-secondary-container/40 border border-secondary/30 text-xs font-medium text-secondary">
                <span class="w-1.5 h-1.5 rounded-full bg-secondary mr-1.5"></span>
                Admin
            </span>
            @else
            <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-surface-container-highest border border-outline-variant/40 text-xs font-medium text-on-surface-variant">
                User
            </span>
            @endif
        </td>
        <td class="px-6 md:px-8 py-4 whitespace-nowrap">
            @if($user->is_active)
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-tertiary/10 border border-tertiary/20 text-xs font-mono text-tertiary">
                <span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span> Aktif
            </span>
            @else
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-error/10 border border-error/20 text-xs font-mono text-error">
                <span class="w-1.5 h-1.5 rounded-full bg-error"></span> Suspended
            </span>
            @endif
        </td>
        <td class="px-6 md:px-8 py-4 text-center whitespace-nowrap">
            <span class="font-mono text-sm text-on-surface-variant">{{ $user->download_logs_count }}</span>
        </td>
        <td class="px-6 md:px-8 py-4 text-right whitespace-nowrap">
            @unless($isSelf)
            <div class="flex justify-end items-center gap-1">
                <!-- Toggle Role -->
                <form method="POST" action="{{ route('admin.users.toggleRole', $user) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-primary/10 transition-colors" title="Toggle Role → {{ $user->role === 'admin' ? 'User' : 'Admin' }}">
                        <span class="material-symbols-outlined text-[18px]">manage_accounts</span>
                    </button>
                </form>

                <!-- Toggle Status -->
                <form method="POST" action="{{ route('admin.users.toggleStatus', $user) }}">
                    @csrf @method('PATCH')
                    @if($user->is_active)
                    <button type="submit" class="p-1.5 rounded-lg text-on-surface-variant hover:text-error hover:bg-error/10 transition-colors" title="Suspend User">
                        <span class="material-symbols-outlined text-[18px]">block</span>
                    </button>
                    @else
                    <button type="submit" class="p-1.5 rounded-lg text-on-surface-variant hover:text-tertiary hover:bg-tertiary/10 transition-colors" title="Aktifkan User">
                        <span class="material-symbols-outlined text-[18px]">settings_backup_restore</span>
                    </button>
                    @endif
                </form>

                <!-- Delete User -->
                <button @click="confirmDelete = true" class="p-1.5 rounded-lg text-on-surface-variant hover:text-error hover:bg-error/10 transition-colors" title="Hapus User">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>

                <!-- Delete Confirmation Modal -->
                <div x-show="confirmDelete" x-cloak x-transition
                     class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center" style="display: none;">
                    <div @click.away="confirmDelete = false" class="bg-surface-container-high border border-outline-variant/20 rounded-2xl p-6 max-w-sm w-full mx-4 shadow-2xl">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="material-symbols-outlined text-error text-2xl">warning</span>
                            <h3 class="text-lg font-bold text-on-surface">Konfirmasi Hapus</h3>
                        </div>
                        <p class="text-sm text-on-surface-variant mb-6">
                            Apakah Anda yakin ingin menghapus akun <strong class="text-on-surface">{{ $user->name }}</strong>?
                            Tindakan ini tidak dapat dibatalkan.
                        </p>
                        <div class="flex justify-end gap-3">
                            <button @click="confirmDelete = false" class="px-4 py-2 rounded-xl text-sm font-medium text-on-surface-variant hover:bg-surface-variant transition-colors">
                                Batal
                            </button>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-4 py-2 rounded-xl text-sm font-medium bg-error text-on-error hover:bg-error/80 transition-colors">
                                    Hapus Permanen
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <span class="text-xs text-on-surface-variant/40 font-mono">—</span>
            @endunless
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="7" class="px-6 md:px-8 py-12 text-center text-on-surface-variant/50">
            <span class="material-symbols-outlined text-3xl mb-2 block">group_off</span>
            <p class="text-sm">Tidak ada pengguna ditemukan.</p>
        </td>
    </tr>
    @endforelse
</x-admin.table>

<!-- Pagination -->
@if($users->hasPages())
<div class="mt-6 flex justify-center">
    {{ $users->links() }}
</div>
@endif
@endsection
