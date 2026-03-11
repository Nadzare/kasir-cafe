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
    @section('title', 'Login - K&D Coffee')
    
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
                <i class="fa-solid fa-envelope mr-2 text-amber-600"></i>
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
                class="w-full px-4 py-3.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-600 focus:border-amber-600 focus:bg-white transition-all duration-200"
                placeholder="nama@email.com"
            />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fa-solid fa-lock mr-2 text-amber-600"></i>
                Password
            </label>
            <input 
                wire:model="form.password" 
                id="password" 
                type="password" 
                name="password" 
                required 
                autocomplete="current-password"
                class="w-full px-4 py-3.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-600 focus:border-amber-600 focus:bg-white transition-all duration-200"
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
                    class="rounded border-gray-300 text-amber-600 shadow-sm focus:ring-amber-600" 
                    name="remember"
                >
                <span class="ms-2 text-sm text-gray-600 font-medium">Ingat Saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-amber-700 hover:text-orange-600 font-semibold transition-colors" href="{{ route('password.request') }}" wire:navigate>
                    Lupa Password?
                </a>
            @endif
        </div>

        <!-- Login Button -->
        <button 
            type="submit"
            class="w-full bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-orange-500/30 hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <i class="fa-solid fa-right-to-bracket mr-2"></i>
            Masuk ke Sistem
        </button>
    </form>

    <!-- Quick Login Hints (Optional) -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <p class="text-xs text-gray-500 text-center mb-3">Demo Credentials:</p>
        <div class="grid grid-cols-2 gap-2 text-xs">
            <div class="bg-orange-50 p-2 rounded-lg text-center">
                <p class="font-semibold text-orange-700">Admin</p>
                <p class="text-orange-600">admin@cafe.com</p>
            </div>
            <div class="bg-amber-50 p-2 rounded-lg text-center">
                <p class="font-semibold text-amber-700">Kasir</p>
                <p class="text-amber-600">kasir@cafe.com</p>
            </div>
        </div>
    </div>
</div>
