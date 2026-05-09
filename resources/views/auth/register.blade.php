@extends('layouts.guest')

@section('title', 'Register - SaveTube')

@section('content')
<div class="bg-surface-variant/60 backdrop-blur-2xl rounded-xl border border-outline-variant/15 p-6 md:p-8 shadow-[0_40px_80px_rgba(227,224,247,0.05)] relative overflow-hidden group">
    {{-- Subtle Inner Glow --}}
    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent pointer-events-none opacity-50"></div>
    
    {{-- Header --}}
    <div class="text-center mb-8 relative z-10">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-surface-container-high border border-outline-variant/20 mb-4 shadow-[0_4px_20px_rgba(227,224,247,0.03)]">
            <span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'FILL' 1;">edit</span>
        </div>
        <h1 class="text-2xl font-bold text-on-surface mt-2 mb-2 tracking-tight">Buat Akun SaveTube</h1>
        <p class="text-on-surface-variant/80 text-sm">Bergabunglah dengan arsip digital pilihan Anda.</p>
    </div>
    
    {{-- Form --}}
    <form action="{{ route('register') }}" class="space-y-5 relative z-10" method="POST">
        @csrf
        
        {{-- Full Name Field --}}
        <div class="space-y-2">
            <label class="block font-label text-sm font-medium text-on-surface" for="fullName">Nama Lengkap</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/70">person</span>
                <input class="w-full bg-surface-container-lowest border border-outline-variant/15 rounded-lg py-3 pl-10 pr-4 text-on-surface placeholder:text-on-surface-variant/50 focus:border-outline-variant/50 focus:ring-1 focus:ring-primary-container focus:outline-none transition-all font-mono text-sm @error('name') border-error/50 @enderror" id="fullName" name="name" placeholder="John Doe" required type="text" value="{{ old('name') }}"/>
            </div>
            @error('name')
            <p class="text-error text-xs">{{ $message }}</p>
            @enderror
        </div>
        
        {{-- Email Field --}}
        <div class="space-y-2">
            <label class="block font-label text-sm font-medium text-on-surface" for="email">Email</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/70">mail</span>
                <input class="w-full bg-surface-container-lowest border border-outline-variant/15 rounded-lg py-3 pl-10 pr-4 text-on-surface placeholder:text-on-surface-variant/50 focus:border-outline-variant/50 focus:ring-1 focus:ring-primary-container focus:outline-none transition-all font-mono text-sm @error('email') border-error/50 @enderror" id="email" name="email" placeholder="john@example.com" required type="email" value="{{ old('email') }}"/>
            </div>
            @error('email')
            <p class="text-error text-xs">{{ $message }}</p>
            @enderror
        </div>
        
        {{-- Password Field with Alpine.js --}}
        <div class="space-y-2" x-data="{ showPassword: false, passwordLength: 0 }">
            <label class="block font-label text-sm font-medium text-on-surface" for="password">Kata Sandi</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/70">lock</span>
                <input class="w-full bg-surface-container-lowest border border-outline-variant/15 rounded-lg py-3 pl-10 pr-10 text-on-surface placeholder:text-on-surface-variant/50 focus:border-outline-variant/50 focus:ring-1 focus:ring-primary-container focus:outline-none transition-all font-mono text-sm @error('password') border-error/50 @enderror" id="password" name="password" placeholder="••••••••" required :type="showPassword ? 'text' : 'password'" @input="passwordLength = $event.target.value.length"/>
                <button @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant/70 hover:text-on-surface transition-colors focus:outline-none" type="button">
                    <span class="material-symbols-outlined text-[20px]" x-text="showPassword ? 'visibility' : 'visibility_off'">visibility_off</span>
                </button>
            </div>
            @error('password')
            <p class="text-error text-xs">{{ $message }}</p>
            @enderror
            {{-- Password Strength (Interactive) --}}
            <div class="pt-1 flex gap-1 h-1.5 w-full">
                <div class="h-full w-1/4 rounded-full transition-colors duration-300" :class="passwordLength > 0 ? 'bg-error-container' : 'bg-surface-variant'"></div>
                <div class="h-full w-1/4 rounded-full transition-colors duration-300" :class="passwordLength >= 4 ? 'bg-[#cba6f7]' : 'bg-surface-variant'"></div>
                <div class="h-full w-1/4 rounded-full transition-colors duration-300" :class="passwordLength >= 6 ? 'bg-[#b4f2af]' : 'bg-surface-variant'"></div>
                <div class="h-full w-1/4 rounded-full transition-colors duration-300" :class="passwordLength >= 8 ? 'bg-[#89c485]' : 'bg-surface-variant'"></div>
            </div>
            <p class="text-xs text-on-surface-variant/70 font-mono mt-1" x-text="passwordLength === 0 ? '' : (passwordLength < 4 ? 'Sandi Lemah' : (passwordLength < 8 ? 'Sandi Sedang' : 'Sandi Kuat'))"></p>
        </div>
        
        {{-- Confirm Password Field --}}
        <div class="space-y-2">
            <label class="block font-label text-sm font-medium text-on-surface" for="password_confirmation">Konfirmasi Kata Sandi</label>
            <div class="relative" x-data="{ match: false, confirmVal: '', passVal: '' }" @keyup.window="passVal = document.getElementById('password').value">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/70">lock_reset</span>
                <input class="w-full bg-surface-container-lowest border border-outline-variant/15 rounded-lg py-3 pl-10 pr-10 text-on-surface placeholder:text-on-surface-variant/50 focus:border-outline-variant/50 focus:ring-1 focus:ring-primary-container focus:outline-none transition-all font-mono text-sm" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required type="password" x-model="confirmVal" @input="match = confirmVal !== '' && confirmVal === passVal"/>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[20px] transition-all duration-300" :class="match ? 'text-[#89c485] opacity-100' : 'text-outline-variant opacity-50'" x-show="confirmVal !== ''">check_circle</span>
            </div>
        </div>
        
        {{-- Submit Button --}}
        <div class="pt-4">
            <button class="w-full relative overflow-hidden bg-gradient-to-br from-primary to-primary-container text-on-primary-fixed font-bold py-3.5 rounded-lg shadow-[0_0_20px_rgba(203,166,247,0.15)] hover:shadow-[0_0_25px_rgba(203,166,247,0.25)] hover:scale-[1.02] transition-all duration-300 group flex items-center justify-center gap-2 focus:outline-none" type="submit">
                <span class="relative z-10">Daftar</span>
                <span class="material-symbols-outlined relative z-10 text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                {{-- Hover Glow --}}
                <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
            </button>
        </div>
    </form>
    
    {{-- Secondary Link --}}
    <div class="mt-8 text-center relative z-10">
        <a class="inline-flex items-center gap-1.5 text-sm text-on-surface-variant hover:text-primary transition-colors focus:outline-none" href="{{ route('login') }}">
            <span>Sudah punya akun? Masuk di sini</span>
            <span class="material-symbols-outlined text-[16px]">login</span>
        </a>
    </div>
</div>
@endsection
