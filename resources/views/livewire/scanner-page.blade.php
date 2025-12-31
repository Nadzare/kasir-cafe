<?php

use App\Models\Transaction;
use App\Models\TicketValidation;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public $ticketCode = '';
    public $scanResult = null;
    public $resultType = ''; // 'success' atau 'error'
    
    public function scanTicket()
    {
        $this->scanResult = null;
        $this->resultType = '';
        
        if (empty($this->ticketCode)) {
            $this->resultType = 'error';
            $this->scanResult = 'Kode tiket tidak boleh kosong!';
            return;
        }
        
        // Cari transaksi berdasarkan UUID
        $transaction = Transaction::where('uuid', $this->ticketCode)
            ->where('status', 'paid')
            ->first();
        
        if (!$transaction) {
            $this->resultType = 'error';
            $this->scanResult = 'TIKET TIDAK VALID! Transaksi tidak ditemukan.';
            $this->playSound('error');
            return;
        }
        
        // Cek apakah sudah pernah di-scan
        $validation = TicketValidation::where('transaction_id', $transaction->id)->first();
        
        if ($validation) {
            $this->resultType = 'error';
            $this->scanResult = 'TIKET SUDAH DIGUNAKAN! Tanggal scan: ' . $validation->formatted_scanned_at;
            $this->playSound('error');
            return;
        }
        
        // Validasi tiket (scan pertama kali)
        try {
            TicketValidation::create([
                'transaction_id' => $transaction->id,
                'scanned_at' => now(),
                'scanned_by' => auth()->id(),
                'status' => 'valid',
            ]);
            
            $this->resultType = 'success';
            $this->scanResult = '✅ SILAKAN MASUK! Tiket Valid - ' . $transaction->customer_name ?: 'Guest';
            $this->playSound('success');
            
            // Auto clear setelah 3 detik
            $this->dispatch('auto-clear');
            
        } catch (\Exception $e) {
            $this->resultType = 'error';
            $this->scanResult = 'Terjadi kesalahan: ' . $e->getMessage();
            $this->playSound('error');
        }
    }
    
    public function playSound($type)
    {
        $this->dispatch('play-sound', type: $type);
    }
    
    public function clearResult()
    {
        $this->reset(['ticketCode', 'scanResult', 'resultType']);
    }
}; ?>

<div>
    @section('title', 'Scanner Tiket | Wisata Tuksirah')
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('🎫 Scanner Tiket - Gatekeeper') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Scanner Form -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Scan QR Code atau Masukkan Kode Tiket
                        </label>
                        <form wire:submit.prevent="scanTicket" class="flex space-x-2">
                            <input 
                                type="text" 
                                wire:model="ticketCode"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg text-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="Masukkan kode UUID tiket..."
                                autofocus
                            >
                            <button 
                                type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-6 py-3 rounded-lg transition duration-200"
                            >
                                🔍 Scan
                            </button>
                        </form>
                    </div>

                    <!-- Result Display -->
                    @if($scanResult)
                        <div 
                            class="p-6 rounded-lg text-center text-xl font-bold border-4 {{ $resultType === 'success' ? 'bg-green-100 border-green-500 text-green-800' : 'bg-red-100 border-red-500 text-red-800' }}"
                        >
                            <div class="text-6xl mb-4">
                                {{ $resultType === 'success' ? '✅' : '❌' }}
                            </div>
                            <div class="mb-4">
                                {{ $scanResult }}
                            </div>
                            <button 
                                wire:click="clearResult"
                                class="mt-4 bg-gray-500 hover:bg-gray-600 text-white font-semibold px-6 py-2 rounded-lg"
                            >
                                Scan Tiket Berikutnya
                            </button>
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-400">
                            <svg class="mx-auto h-24 w-24 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            <p class="text-xl">Siap untuk scan tiket</p>
                            <p class="text-sm mt-2">Scan QR Code atau masukkan kode tiket secara manual</p>
                        </div>
                    @endif

                    <!-- Info Box -->
                    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="font-semibold text-blue-800 mb-2">📝 Petunjuk:</h3>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>• Scan QR Code dari struk tiket pengunjung</li>
                            <li>• Atau masukkan kode UUID tiket secara manual</li>
                            <li>• Tiket yang sudah di-scan tidak dapat digunakan lagi</li>
                            <li>• Pastikan koneksi internet stabil</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Statistics Today -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-lg mb-4">📊 Statistik Hari Ini</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">
                                {{ \App\Models\TicketValidation::whereDate('scanned_at', today())->count() }}
                            </div>
                            <div class="text-sm text-gray-600">Tiket Ter-scan</div>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">
                                {{ \App\Models\Transaction::whereDate('created_at', today())->where('status', 'paid')->count() }}
                            </div>
                            <div class="text-sm text-gray-600">Total Transaksi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    // Auto clear result after 3 seconds on success
    $wire.on('auto-clear', () => {
        setTimeout(() => {
            $wire.call('clearResult');
        }, 3000);
    });
    
    // Play sound effect
    $wire.on('play-sound', (event) => {
        const type = event.type;
        // You can implement actual sound here using Web Audio API
        console.log('Play sound:', type);
    });
</script>
@endscript
