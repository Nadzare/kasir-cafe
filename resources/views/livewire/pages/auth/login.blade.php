<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    @section('title', 'Login - Wisata Tuksirah')
    
    <!-- Welcome Message -->
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang!</h2>
        <p class="text-gray-600 font-medium">Login untuk melanjutkan ke sistem</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-6">
        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fa-solid fa-envelope mr-2 text-[#1a4d2e]"></i>
                Email
            </label>
            <input 
                wire:model="form.email" 
                id="email" 
                type="email" 
                name="email" 
                required 
                autofocus 
                autocomplete="username"
                class="w-full px-4 py-3.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#1a4d2e] focus:border-[#1a4d2e] focus:bg-white transition-all duration-200"
                placeholder="nama@email.com"
            />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fa-solid fa-lock mr-2 text-[#1a4d2e]"></i>
                Password
            </label>
            <input 
                wire:model="form.password" 
                id="password" 
                type="password" 
                name="password" 
                required 
                autocomplete="current-password"
                class="w-full px-4 py-3.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#1a4d2e] focus:border-[#1a4d2e] focus:bg-white transition-all duration-200"
                placeholder="••••••••"
            />
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember" class="inline-flex items-center cursor-pointer">
                <input 
                    wire:model="form.remember" 
                    id="remember" 
                    type="checkbox" 
                    class="rounded border-gray-300 text-[#1a4d2e] shadow-sm focus:ring-[#1a4d2e]" 
                    name="remember"
                >
                <span class="ms-2 text-sm text-gray-600 font-medium">Ingat Saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-[#1a4d2e] hover:text-[#2d7a4f] font-semibold transition-colors" href="{{ route('password.request') }}" wire:navigate>
                    Lupa Password?
                </a>
            @endif
        </div>

        <!-- Login Button -->
        <button 
            type="submit"
            class="w-full bg-gradient-to-r from-[#1a4d2e] to-[#2d7a4f] hover:from-[#143d24] hover:to-[#1a4d2e] text-white font-bold py-4 rounded-xl shadow-lg shadow-green-500/30 hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <i class="fa-solid fa-right-to-bracket mr-2"></i>
            Masuk ke Sistem
        </button>
    </form>

    <!-- Quick Login Hints (Optional) -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <p class="text-xs text-gray-500 text-center mb-3">Demo Credentials:</p>
        <div class="grid grid-cols-3 gap-2 text-xs">
            <div class="bg-green-50 p-2 rounded-lg text-center">
                <p class="font-semibold text-green-700">Admin</p>
                <p class="text-green-600">admin@</p>
            </div>
            <div class="bg-blue-50 p-2 rounded-lg text-center">
                <p class="font-semibold text-blue-700">Kasir</p>
                <p class="text-blue-600">kasir@</p>
            </div>
            <div class="bg-purple-50 p-2 rounded-lg text-center">
                <p class="font-semibold text-purple-700">Gate</p>
                <p class="text-purple-600">gate@</p>
            </div>
        </div>
    </div>
</div>
