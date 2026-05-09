@extends('layouts.guest')

@section('title', 'Login - SaveTube')

@section('content')
<div class="glass-panel rounded-2xl p-6 md:p-8 shadow-[0_4px_40px_rgba(227,224,247,0.05)] transform transition-transform hover:scale-[1.01] duration-500 ease-out">
    {{-- Title Section --}}
    <div class="flex items-center justify-center gap-3 mb-8">
        <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: 'FILL' 1;">key</span>
        <h2 class="font-headline font-bold text-2xl text-on-surface tracking-tight">Masuk ke SaveTube</h2>
    </div>

    {{-- Flash Success Message --}}
    @if(session('success'))
    <div class="mb-6 p-3 rounded-lg bg-tertiary/10 border border-tertiary/20 text-sm text-tertiary">
        {{ session('success') }}
    </div>
    @endif
    
    {{-- Login Form --}}
    <form action="{{ route('login') }}" class="space-y-6" method="POST">
        @csrf
        {{-- Email Field --}}
        <div>
            <label class="block font-label text-sm font-medium text-on-surface-variant mb-2" for="email">Email Address</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <span class="material-symbols-outlined text-outline text-xl">mail</span>
                </div>
                <input class="block w-full pl-11 pr-4 py-3 bg-surface-container-lowest border border-outline-variant/15 rounded-lg text-on-surface placeholder-on-surface-variant/50 focus:outline-none input-focus-pulse transition-all duration-300 font-mono text-sm @error('email') border-error/50 @enderror" id="email" name="email" placeholder="user@example.com" required type="email" value="{{ old('email') }}"/>
            </div>
            @error('email')
            <p class="text-error text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>
        
        {{-- Password Field (with Alpine js toggle) --}}
        <div x-data="{ showPassword: false }">
            <label class="block font-label text-sm font-medium text-on-surface-variant mb-2" for="password">Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <span class="material-symbols-outlined text-outline text-xl">lock</span>
                </div>
                <input class="block w-full pl-11 pr-12 py-3 bg-surface-container-lowest border border-outline-variant/15 rounded-lg text-on-surface placeholder-on-surface-variant/50 focus:outline-none input-focus-pulse transition-all duration-300 font-mono text-sm" id="password" name="password" placeholder="••••••••" required :type="showPassword ? 'text' : 'password'"/>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                    <button @click="showPassword = !showPassword" class="text-outline hover:text-primary transition-colors focus:outline-none" type="button">
                        <span class="material-symbols-outlined text-xl" x-text="showPassword ? 'visibility_off' : 'visibility'">visibility</span>
                    </button>
                </div>
            </div>
        </div>
        
        {{-- Remember Me & Forgot Password --}}
        <div class="flex items-center justify-between pt-2">
            <div class="flex items-center">
                <input class="h-4 w-4 rounded border-outline-variant/30 bg-surface-container-lowest text-primary focus:ring-primary focus:ring-offset-[#11111b] focus:ring-offset-2" id="remember-me" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }}/>
                <label class="ml-2 block text-sm font-label text-on-surface-variant" for="remember-me">
                    Ingat saya
                </label>
            </div>
        </div>
        
        {{-- Primary Action Button --}}
        <div class="pt-4">
            <button class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg text-base font-headline font-bold text-on-primary bg-gradient-to-br from-primary to-primary-container shadow-lg hover:shadow-primary/20 hover:scale-[1.02] transform transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-[#11111b] focus:ring-primary" type="submit">
                Masuk
                <span class="material-symbols-outlined ml-2 text-xl">arrow_forward</span>
            </button>
        </div>
    </form>
    
    {{-- Secondary Action --}}
    <div class="mt-8 text-center">
        <p class="text-sm font-label text-on-surface-variant">
            Belum punya akun? 
            <a class="font-medium text-secondary hover:text-primary transition-colors ml-1 underline decoration-secondary/30 underline-offset-4 hover:decoration-primary" href="{{ route('register') }}">
                Daftar di sini
            </a>
        </p>
    </div>
</div>
@endsection
