<?php

use Livewire\Volt\Component;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\Expense;
use Carbon\Carbon;

new class extends Component {
    public function with()
    {
        $today = Carbon::today();

        // Query untuk transaksi hari ini yang sudah paid
        $todayTransactions = Transaction::whereDate('created_at', $today)
            ->where('status', 'paid');

        // 1. Total Pendapatan Hari Ini (Sum total_amount transaksi 'paid')
        $incomeToday = (clone $todayTransactions)->sum('total_amount');

        // 2. Tiket Terjual Hari Ini (Count quantity dari transaction_items untuk produk type 'ticket')
        $ticketsSold = TransactionItem::whereHas('transaction', function($query) use ($today) {
            $query->whereDate('created_at', $today)
                  ->where('status', 'paid');
        })
        ->whereHas('product', function($query) {
            $query->where('type', 'ticket');
        })
        ->sum('quantity');

        // 3. Kendaraan Masuk (Count quantity dari transaction_items untuk produk type 'parking')
        $vehiclesIn = TransactionItem::whereHas('transaction', function($query) use ($today) {
            $query->whereDate('created_at', $today)
                  ->where('status', 'paid');
        })
        ->whereHas('product', function($query) {
            $query->where('type', 'parking');
        })
        ->sum('quantity');

        // 4. Pengeluaran Operasional Hari Ini
        // Pastikan tabel expenses ada, jika belum, return 0 dulu.
        $expenseToday = \Illuminate\Support\Facades\Schema::hasTable('expenses') 
            ? Expense::whereDate('date', $today)->sum('amount') 
            : 0;

        return [
            'incomeToday' => $incomeToday,
            'ticketsSold' => $ticketsSold,
            'vehiclesIn' => $vehiclesIn,
            'expenseToday' => $expenseToday,
        ];
    }
};
?>

<div>
    <!-- Gradient Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 lg:gap-6 mb-6 lg:mb-8">
        <!-- Card 1: Total Pendapatan -->
        <div class="bg-gradient-to-r from-blue-500 to-cyan-400 rounded-xl lg:rounded-2xl shadow-xl p-4 lg:p-6 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-xs lg:text-sm font-medium mb-1 lg:mb-2">Pendapatan Hari Ini</p>
                    <h3 class="text-2xl lg:text-3xl font-bold">
                        @if($incomeToday >= 1000000)
                            Rp {{ number_format($incomeToday / 1000000, 1, ',', '.') }}M
                        @elseif($incomeToday >= 1000)
                            Rp {{ number_format($incomeToday / 1000, 0, ',', '.') }}K
                        @else
                            Rp {{ number_format($incomeToday, 0, ',', '.') }}
                        @endif
                    </h3>
                </div>
                <div class="bg-white/20 p-2 lg:p-3 rounded-full">
                    <i class="fa-solid fa-rupiah-sign text-xl lg:text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Card 2: Tiket Terjual -->
        <div class="bg-gradient-to-r from-emerald-500 to-green-400 rounded-xl lg:rounded-2xl shadow-xl p-4 lg:p-6 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-xs lg:text-sm font-medium mb-1 lg:mb-2">Tiket Terjual</p>
                    <h3 class="text-2xl lg:text-3xl font-bold">{{ number_format($ticketsSold) }}</h3>
                </div>
                <div class="bg-white/20 p-2 lg:p-3 rounded-full">
                    <i class="fa-solid fa-ticket text-xl lg:text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Card 3: Total Parkir -->
        <div class="bg-gradient-to-r from-orange-500 to-yellow-400 rounded-xl lg:rounded-2xl shadow-xl p-4 lg:p-6 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-xs lg:text-sm font-medium mb-1 lg:mb-2">Kendaraan Masuk</p>
                    <h3 class="text-2xl lg:text-3xl font-bold">{{ number_format($vehiclesIn) }}</h3>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fa-solid fa-car text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Card 4: Pengeluaran -->
        <div class="bg-gradient-to-r from-rose-500 to-pink-500 rounded-2xl shadow-xl p-6 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-sm font-medium mb-2">Pengeluaran Ops</p>
                    <h3 class="text-3xl font-bold">
                        @if($expenseToday >= 1000000)
                            Rp {{ number_format($expenseToday / 1000000, 1, ',', '.') }}M
                        @elseif($expenseToday >= 1000)
                            Rp {{ number_format($expenseToday / 1000, 0, ',', '.') }}K
                        @else
                            Rp {{ number_format($expenseToday, 0, ',', '.') }}
                        @endif
                    </h3>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fa-solid fa-wallet text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>
