<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\PosPage;
use App\Livewire\GateScanPage;
use App\Http\Controllers\TransactionController;

// Redirect root URL langsung ke Login (Internal System)
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('profile', 'profile')->name('profile');
    
    // POS Routes (Kasir) - Using Class-based Component
    Route::get('pos', PosPage::class)->name('pos.index');
    
    // Ticket Print / Struk (Receipt)
    Route::get('struk/{uuid}', [TransactionController::class, 'show'])->name('ticket.print');
    
    // Scanner Routes (Gatekeeper) - Using Class-based Component
    Route::get('scanner', GateScanPage::class)->name('scanner.index');
    
    // Reports & Finance (Admin Only)
    Volt::route('reports', 'report-page')->name('reports.index');
    
    // Admin Dashboard
    Volt::route('admin/dashboard', 'admin-dashboard')->name('admin.dashboard');
});

require __DIR__.'/auth.php';
