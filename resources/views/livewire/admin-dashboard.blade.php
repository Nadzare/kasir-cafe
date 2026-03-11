<?php

use App\Models\Transaction;
use App\Models\Expense;
use App\Models\TransactionItem;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

new #[Layout('layouts.app')] class extends Component {
    public $startDate;
    public $endDate;
    public $transactions = [];
    public $stats = [];
    
    public function mount()
    {
        // Default tanggal hari ini
        $this->startDate = today()->format('Y-m-d');
        $this->endDate = today()->format('Y-m-d');
        $this->loadData();
    }
    
    public function loadData()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();
        
        // Load transaksi dengan items
        $this->transactions = Transaction::with(['cashier', 'items.product'])
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Hitung statistik
        $this->calculateStats($start, $end);
    }
    
    public function calculateStats($start, $end)
    {
        // Total pendapatan
        $totalRevenue = Transaction::whereBetween('created_at', [$start, $end])
            ->where('status', 'paid')
            ->sum(\DB::raw('total_amount - discount_amount'));
        
        // Total pengeluaran
        $totalExpenses = Expense::whereBetween('date', [$start, $end])
            ->sum('amount');
        
        // Pendapatan dari makanan
        $foodRevenue = TransactionItem::whereHas('transaction', function($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end])
              ->where('status', 'paid');
        })
        ->whereHas('product', function($q) {
            $q->where('type', 'makanan');
        })
        ->sum('subtotal');
        
        // Pendapatan dari minuman
        $drinkRevenue = TransactionItem::whereHas('transaction', function($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end])
              ->where('status', 'paid');
        })
        ->whereHas('product', function($q) {
            $q->where('type', 'minuman');
        })
        ->sum('subtotal');
        
        // Pendapatan dari dessert
        $dessertRevenue = TransactionItem::whereHas('transaction', function($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end])
              ->where('status', 'paid');
        })
        ->whereHas('product', function($q) {
            $q->where('type', 'dessert');
        })
        ->sum('subtotal');
        
        // Total transaksi
        $totalTransactions = Transaction::whereBetween('created_at', [$start, $end])
            ->where('status', 'paid')
            ->count();
        
        // Total item terjual (total quantity semua produk)
        $totalItemsSold = TransactionItem::whereHas('transaction', function($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end])
              ->where('status', 'paid');
        })
        ->sum('quantity');
        
        $this->stats = [
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_income' => $totalRevenue - $totalExpenses,
            'food_revenue' => $foodRevenue,
            'drink_revenue' => $drinkRevenue,
            'dessert_revenue' => $dessertRevenue,
            'total_transactions' => $totalTransactions,
            'total_items_sold' => $totalItemsSold,
        ];
    }
    
    public function applyFilter()
    {
        $this->loadData();
    }
    
    public function setToday()
    {
        $this->startDate = today()->format('Y-m-d');
        $this->endDate = today()->format('Y-m-d');
        $this->loadData();
    }
    
    public function setThisWeek()
    {
        $this->startDate = now()->startOfWeek()->format('Y-m-d');
        $this->endDate = now()->endOfWeek()->format('Y-m-d');
        $this->loadData();
    }
    
    public function setThisMonth()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        $this->loadData();
    }
}; ?>

<div>
    @section('title', 'Dashboard Admin | Wisata Tuksirah')
    
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-gray-900 leading-tight flex items-center">
                <i class="fa-solid fa-chart-line mr-3 text-[#1a4d2e]"></i>
                Dashboard Admin - Laporan
            </h2>
            <p class="text-sm text-gray-500 mt-1">Analisa pendapatan dan pengeluaran operasional</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter Section -->
            <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-6 mb-4 lg:mb-6">
                <h3 class="text-base lg:text-lg font-bold text-gray-900 mb-3 lg:mb-4 flex items-center">
                    <i class="fa-solid fa-filter mr-2 text-gray-400"></i>
                    Filter Tanggal
                </h3>
                <div class="flex flex-col sm:flex-row flex-wrap gap-2 lg:gap-3 items-stretch sm:items-end">
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fa-solid fa-calendar-days mr-2 text-gray-400"></i>
                            Dari Tanggal
                        </label>
                        <input 
                            type="date" 
                            wire:model="startDate"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#1a4d2e] focus:border-transparent transition-all"
                        >
                    </div>
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fa-solid fa-calendar-days mr-2 text-gray-400"></i>
                            Sampai Tanggal
                        </label>
                        <input 
                            type="date" 
                            wire:model="endDate"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#1a4d2e] focus:border-transparent transition-all"
                        >
                    </div>
                    <button 
                        wire:click="applyFilter"
                        class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-500/30"
                    >
                        <i class="fa-solid fa-magnifying-glass mr-2"></i>
                        Tampilkan
                    </button>
                    
                    <!-- Quick Filters -->
                    <div class="border-l border-gray-200 pl-3 ml-3 flex gap-2">
                        <button 
                            wire:click="setToday"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-4 py-2 rounded-xl text-sm transition-all"
                        >
                            Hari Ini
                        </button>
                        <button 
                            wire:click="setThisWeek"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-4 py-2 rounded-xl text-sm transition-all"
                        >
                            Minggu Ini
                        </button>
                        <button 
                            wire:click="setThisMonth"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-4 py-2 rounded-xl text-sm transition-all"
                        >
                            Bulan Ini
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 lg:gap-6 mb-4 lg:mb-6">
                <div class="bg-gradient-to-br from-amber-500 to-orange-600 text-white p-4 lg:p-6 rounded-xl lg:rounded-2xl shadow-xl">
                    <div class="flex items-center justify-between mb-2 lg:mb-3">
                        <span class="text-white/80 text-xs lg:text-sm font-medium">Total Pendapatan</span>
                        <div class="bg-white/20 p-2 lg:p-2.5 rounded-lg lg:rounded-xl">
                            <i class="fa-solid fa-arrow-trend-up text-lg lg:text-xl"></i>
                        </div>
                    </div>
                    <div class="text-2xl lg:text-3xl font-bold mb-1">Rp {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}</div>
                    <div class="text-xs text-white/70">
                        <i class="fa-solid fa-coins mr-1"></i>
                        Revenue
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-red-500 to-red-600 text-white p-6 rounded-2xl shadow-xl">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-white/80 text-sm font-medium">Total Pengeluaran</span>
                        <div class="bg-white/20 p-2.5 rounded-xl">
                            <i class="fa-solid fa-arrow-trend-down text-xl"></i>
                        </div>
                    </div>
                    <div class="text-3xl font-bold mb-1">Rp {{ number_format($stats['total_expenses'] ?? 0, 0, ',', '.') }}</div>
                    <div class="text-xs text-white/70">
                        <i class="fa-solid fa-wallet mr-1"></i>
                        Expenses
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-2xl shadow-xl">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-white/80 text-sm font-medium">Pendapatan Bersih</span>
                        <div class="bg-white/20 p-2.5 rounded-xl">
                            <i class="fa-solid fa-chart-line text-xl"></i>
                        </div>
                    </div>
                    <div class="text-3xl font-bold mb-1">Rp {{ number_format($stats['net_income'] ?? 0, 0, ',', '.') }}</div>
                    <div class="text-xs text-white/70">
                        <i class="fa-solid fa-sack-dollar mr-1"></i>
                        Net Income
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-6 rounded-2xl shadow-xl">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-white/80 text-sm font-medium">Total Item Terjual</span>
                        <div class="bg-white/20 p-2.5 rounded-xl">
                            <i class="fa-solid fa-boxes-stacked text-xl"></i>
                        </div>
                    </div>
                    <div class="text-3xl font-bold mb-1">{{ number_format($stats['total_items_sold'] ?? 0) }}</div>
                    <div class="text-xs text-white/70">
                        <i class="fa-solid fa-shopping-basket mr-1"></i>
                        Items Sold
                    </div>
                </div>
            </div>

            <!-- Revenue Breakdown -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 lg:gap-6 mb-4 lg:mb-6">
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-orange-600">Rp {{ number_format($stats['food_revenue'] ?? 0, 0, ',', '.') }}</div>
                            <div class="text-sm text-gray-600 mt-2 font-medium">Pendapatan Makanan</div>
                        </div>
                        <div class="bg-orange-50 p-4 rounded-2xl">
                            <i class="fa-solid fa-utensils text-4xl text-orange-500"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-blue-600">Rp {{ number_format($stats['drink_revenue'] ?? 0, 0, ',', '.') }}</div>
                            <div class="text-sm text-gray-600 mt-2 font-medium">Pendapatan Minuman</div>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-2xl">
                            <i class="fa-solid fa-mug-hot text-4xl text-blue-500"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-pink-600">Rp {{ number_format($stats['dessert_revenue'] ?? 0, 0, ',', '.') }}</div>
                            <div class="text-sm text-gray-600 mt-2 font-medium">Pendapatan Dessert</div>
                        </div>
                        <div class="bg-pink-50 p-4 rounded-2xl">
                            <i class="fa-solid fa-ice-cream text-4xl text-pink-500"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <i class="fa-solid fa-rectangle-list mr-2 text-gray-400"></i>
                        Detail Transaksi
                    </h3>
                </div>
                <div class="p-6">
                    @if($transactions->count() > 0)
                        <div class="overflow-x-auto rounded-xl border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kasir</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Items</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Metode</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($transactions as $index => $transaction)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">#{{ $transaction->id }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $transaction->cashier->name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction->customer_name ?: '-' }}</td>
                                            <td class="px-6 py-4 text-sm">
                                                <div class="space-y-1">
                                                    @foreach($transaction->items as $item)
                                                        <div class="text-xs text-gray-600">
                                                            <i class="fa-solid fa-circle-check text-green-500 mr-1"></i>
                                                            {{ $item->product->name }} ({{ $item->quantity }}x)
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">
                                                Rp {{ number_format($transaction->final_amount, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-center">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $transaction->payment_method === 'cash' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                                    <i class="fa-solid {{ $transaction->payment_method === 'cash' ? 'fa-money-bill' : 'fa-qrcode' }} mr-1"></i>
                                                    {{ strtoupper($transaction->payment_method) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-400">
                            <i class="fa-solid fa-inbox text-6xl mb-4"></i>
                            <p class="text-lg font-medium">Tidak ada transaksi pada periode ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
