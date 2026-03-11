<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Transaction;
use App\Models\Expense;
use Carbon\Carbon;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    // Filter Properties
    public $startDate;
    public $endDate;

    // Expense Form Properties
    public $description = '';
    public $amount = '';

    public function mount()
    {
        // Default filter: Bulan ini
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function applyFilter()
    {
        $this->resetPage();
    }

    public function saveExpense()
    {
        $this->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        Expense::create([
            'description' => $this->description,
            'amount' => $this->amount,
            'date' => now(),
            'user_id' => auth()->id(),
        ]);

        session()->flash('success', 'Pengeluaran berhasil disimpan!');

        // Reset form
        $this->description = '';
        $this->amount = '';
        $this->resetPage();
    }

    public function deleteExpense($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        session()->flash('success', 'Pengeluaran berhasil dihapus!');
        $this->resetPage();
    }

    public function printReport()
    {
        session()->flash('info', 'Fitur cetak laporan akan segera hadir!');
    }

    public function with()
    {
        // Query Transactions dengan filter tanggal
        $transactions = Transaction::with('cashier')
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ])
            ->where('status', 'paid')
            ->latest()
            ->paginate(10, ['*'], 'transactionsPage');

        // Query Expenses dengan filter tanggal
        $expenses = Expense::with('user')
            ->whereBetween('date', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ])
            ->latest('date')
            ->paginate(10, ['*'], 'expensesPage');

        // Calculate Totals
        $totalIncome = Transaction::whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ])
            ->where('status', 'paid')
            ->sum('total_amount');

        $totalExpense = Expense::whereBetween('date', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ])
            ->sum('amount');

        $netProfit = $totalIncome - $totalExpense;

        return [
            'transactions' => $transactions,
            'expenses' => $expenses,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netProfit' => $netProfit,
        ];
    }
};
?>

<div x-data="{ activeTab: 'income' }">
    @section('title', 'Laporan Keuangan | Wisata Tuksirah')
    
    <!-- Page Header -->
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">Laporan & Keuangan</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola pemasukan dan pengeluaran operasional</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-800 px-5 py-4 rounded-r-lg shadow-sm flex items-start" role="alert">
                    <i class="fa-solid fa-check-circle mr-3 mt-0.5"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('info'))
                <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 text-blue-800 px-5 py-4 rounded-r-lg shadow-sm flex items-start" role="alert">
                    <i class="fa-solid fa-info-circle mr-3 mt-0.5"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-4 rounded-r-lg shadow-sm" role="alert">
                    <i class="fa-solid fa-exclamation-circle mr-3"></i>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Filter Section -->
            <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-6 mb-4 lg:mb-6">
                <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-end gap-3 lg:gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fa-solid fa-calendar-days mr-2 text-gray-400"></i>
                            Tanggal Mulai
                        </label>
                        <input type="date" wire:model="startDate" 
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#1a4d2e] focus:border-transparent transition-all">
                    </div>

                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fa-solid fa-calendar-days mr-2 text-gray-400"></i>
                            Tanggal Akhir
                        </label>
                        <input type="date" wire:model="endDate" 
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#1a4d2e] focus:border-transparent transition-all">
                    </div>

                    <button wire:click="applyFilter" 
                        class="px-6 py-2.5 bg-amber-600 text-white font-semibold rounded-xl hover:bg-orange-600 transition-all shadow-lg shadow-orange-500/30">
                        <i class="fa-solid fa-filter mr-2"></i>
                        Terapkan Filter
                    </button>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-6 mb-4 lg:mb-6">
                <!-- Total Pemasukan -->
                <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl shadow-xl p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-white/80 text-sm font-medium">Total Pemasukan</span>
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="fa-solid fa-arrow-trend-up text-lg"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </h3>
                </div>

                <!-- Total Pengeluaran -->
                <div class="bg-gradient-to-br from-red-500 to-pink-600 rounded-2xl shadow-xl p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-white/80 text-sm font-medium">Total Pengeluaran</span>
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="fa-solid fa-arrow-trend-down text-lg"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold">
                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </h3>
                </div>

                <!-- Laba Bersih -->
                <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl shadow-xl p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-white/80 text-sm font-medium">Laba Bersih</span>
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="fa-solid fa-chart-pie text-lg"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold">
                        Rp {{ number_format($netProfit, 0, ',', '.') }}
                    </h3>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="bg-white rounded-t-2xl shadow-sm border border-gray-100 border-b-0">
                <div class="flex border-b border-gray-200">
                    <button @click="activeTab = 'income'" 
                        :class="{ 'border-b-2 border-[#1a4d2e] text-[#1a4d2e]': activeTab === 'income', 'text-gray-500 hover:text-gray-700': activeTab !== 'income' }"
                        class="flex-1 px-6 py-4 font-semibold transition-all">
                        <i class="fa-solid fa-money-bill-trend-up mr-2"></i>
                        Laporan Pemasukan
                    </button>
                    <button @click="activeTab = 'expense'" 
                        :class="{ 'border-b-2 border-[#1a4d2e] text-[#1a4d2e]': activeTab === 'expense', 'text-gray-500 hover:text-gray-700': activeTab !== 'expense' }"
                        class="flex-1 px-6 py-4 font-semibold transition-all">
                        <i class="fa-solid fa-wallet mr-2"></i>
                        Biaya Operasional
                    </button>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="bg-white rounded-b-2xl shadow-sm border border-gray-100 p-6">
                <!-- Tab 1: Laporan Pemasukan -->
                <div x-show="activeTab === 'income'" x-transition>
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Daftar Transaksi</h3>
                        <button wire:click="printReport" 
                            class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 transition-all">
                            <i class="fa-solid fa-print mr-2"></i>
                            Cetak Laporan
                        </button>
                    </div>

                    @if($transactions->count() > 0)
                        <!-- Table -->
                        <div class="overflow-x-auto rounded-xl border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kode Transaksi</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kasir</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Metode</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($transactions as $transaction)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="font-mono text-sm font-semibold text-gray-700">
                                                    {{ strtoupper(substr($transaction->uuid, 0, 8)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ $transaction->cashier->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold 
                                                    {{ $transaction->payment_method === 'cash' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                                    <i class="fa-solid {{ $transaction->payment_method === 'cash' ? 'fa-money-bill' : 'fa-qrcode' }} mr-1"></i>
                                                    {{ ucfirst($transaction->payment_method) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-green-600">
                                                Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-green-50 border-t-2 border-green-200">
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-right text-sm font-bold text-gray-700">
                                            TOTAL PEMASUKAN PERIODE INI:
                                        </td>
                                        <td class="px-6 py-4 text-right text-lg font-bold text-green-600">
                                            Rp {{ number_format($totalIncome, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $transactions->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-12">
                            <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-inbox text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 font-medium">Tidak ada transaksi pada periode ini</p>
                        </div>
                    @endif
                </div>

                <!-- Tab 2: Biaya Operasional -->
                <div x-show="activeTab === 'expense'" x-transition>
                    <!-- Form Input Pengeluaran -->
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 mb-6 border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fa-solid fa-plus-circle mr-2 text-[#1a4d2e]"></i>
                            Tambah Pengeluaran Baru
                        </h3>

                        <form wire:submit.prevent="saveExpense" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                                <input type="text" wire:model="description" placeholder="Contoh: Beli Token Listrik" 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1a4d2e] focus:border-transparent transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nominal (Rp)</label>
                                <input type="number" wire:model="amount" placeholder="50000" 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1a4d2e] focus:border-transparent transition-all">
                            </div>

                            <div class="md:col-span-3">
                                <button type="submit" 
                                    class="w-full md:w-auto px-6 py-2.5 bg-amber-600 text-white font-semibold rounded-xl hover:bg-orange-600 transition-all shadow-lg shadow-orange-500/30">
                                    <i class="fa-solid fa-save mr-2"></i>
                                    Simpan Pengeluaran
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Tabel Pengeluaran -->
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Daftar Pengeluaran</h3>

                    @if($expenses->count() > 0)
                        <div class="overflow-x-auto rounded-xl border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Deskripsi</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Dibuat Oleh</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Nominal</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($expenses as $expense)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ Carbon\Carbon::parse($expense->date)->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-700">
                                                {{ $expense->description }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ $expense->user->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-red-600">
                                                Rp {{ number_format($expense->amount, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <button wire:click="deleteExpense({{ $expense->id }})" 
                                                    wire:confirm="Yakin ingin menghapus pengeluaran ini?"
                                                    class="text-red-600 hover:text-red-800 font-semibold">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-red-50 border-t-2 border-red-200">
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-right text-sm font-bold text-gray-700">
                                            TOTAL PENGELUARAN PERIODE INI:
                                        </td>
                                        <td class="px-6 py-4 text-right text-lg font-bold text-red-600">
                                            Rp {{ number_format($totalExpense, 0, ',', '.') }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $expenses->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-12">
                            <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-wallet text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 font-medium">Belum ada pengeluaran pada periode ini</p>
                            <p class="text-sm text-gray-400 mt-2">Gunakan form di atas untuk mencatat pengeluaran operasional</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
