<?php

use Livewire\Volt\Component;
use App\Models\Transaction;

new class extends Component {
    public function with()
    {
        // Ambil 5 transaksi terbaru dengan relasi cashier (user)
        $transactions = Transaction::with('cashier')
            ->latest()
            ->take(5)
            ->get();

        return [
            'transactions' => $transactions,
        ];
    }
};
?>

<div>
    <!-- Recent Transactions Card -->
    <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-6">
        <div class="flex items-center justify-between mb-4 lg:mb-6">
            <div>
                <h3 class="text-base lg:text-lg font-bold text-gray-900">Transaksi Terbaru</h3>
                <p class="text-xs lg:text-sm text-gray-500 mt-1">5 Transaksi Terakhir</p>
            </div>
            <div class="bg-blue-50 p-2 lg:p-3 rounded-lg lg:rounded-xl">
                <i class="fa-solid fa-receipt text-lg lg:text-xl text-blue-600"></i>
            </div>
        </div>

        @if($transactions->count() > 0)
            <!-- Table -->
            <div class="space-y-2 lg:space-y-3">
                @foreach($transactions as $transaction)
                    <div class="flex items-center justify-between p-3 lg:p-4 bg-gray-50 rounded-lg lg:rounded-xl hover:bg-gray-100 transition-colors border border-gray-100">
                        <div class="flex-1">
                            <!-- Kode Tiket -->
                            <div class="flex items-center space-x-1.5 lg:space-x-2 mb-1 lg:mb-2">
                                <span class="font-mono text-xs lg:text-sm font-bold text-gray-900">
                                    {{ strtoupper(substr($transaction->uuid, 0, 8)) }}
                                </span>
                                @if($transaction->status === 'paid')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <i class="fa-solid fa-check-circle mr-1"></i>
                                        Paid
                                    </span>
                                @elseif($transaction->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        <i class="fa-solid fa-clock mr-1"></i>
                                        Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        <i class="fa-solid fa-times-circle mr-1"></i>
                                        Cancel
                                    </span>
                                @endif
                            </div>

                            <!-- Info -->
                            <div class="flex items-center space-x-4 text-xs text-gray-600">
                                <span class="flex items-center">
                                    <i class="fa-solid fa-user mr-1.5 text-gray-400"></i>
                                    {{ $transaction->cashier->name ?? 'N/A' }}
                                </span>
                                <span class="flex items-center">
                                    <i class="fa-solid fa-clock mr-1.5 text-gray-400"></i>
                                    {{ $transaction->created_at->format('H:i') }}
                                </span>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="text-right ml-3 lg:ml-4">
                            <p class="text-base lg:text-lg font-bold text-[#1a4d2e]">
                                Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ ucfirst($transaction->payment_method) }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- View All Link -->
            <div class="mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-center text-sm font-semibold text-[#1a4d2e] hover:text-[#2d7a4f] transition-colors">
                    Lihat Semua Transaksi
                    <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-receipt text-2xl text-gray-400"></i>
                </div>
                <p class="text-gray-500 font-medium mb-2">Belum Ada Transaksi</p>
                <p class="text-sm text-gray-400">Transaksi akan muncul di sini setelah kasir melakukan penjualan</p>
                
                @if(auth()->user()->role === 'kasir' || auth()->user()->role === 'admin')
                    <a href="{{ route('pos.index') }}" class="inline-flex items-center mt-4 px-4 py-2 bg-[#1a4d2e] text-white text-sm font-semibold rounded-lg hover:bg-[#2d7a4f] transition-colors">
                        <i class="fa-solid fa-plus mr-2"></i>
                        Buat Transaksi Baru
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
