<?php

use App\Models\Transaction;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.print')] class extends Component {
    public $transaction;
    
    public function mount($uuid)
    {
        $this->transaction = Transaction::with(['cashier', 'items.product'])
            ->where('uuid', $uuid)
            ->firstOrFail();
    }
}; ?>

<div class="max-w-md mx-auto bg-white p-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold">TUKSIRAH KALI PEMALI</h1>
        <p class="text-sm text-gray-600">Wisata Alam & Rekreasi</p>
        <p class="text-xs text-gray-500">Jl. Raya Tuksirah, Kec. Pemali</p>
        <p class="text-xs text-gray-500">Telp: (0281) 123-4567</p>
    </div>

    <div class="border-t-2 border-b-2 border-dashed py-3 mb-4">
        <div class="text-xs space-y-1">
            <div class="flex justify-between">
                <span>No. Transaksi:</span>
                <span class="font-mono">#{{ $transaction->id }}</span>
            </div>
            <div class="flex justify-between">
                <span>Tanggal:</span>
                <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Kasir:</span>
                <span>{{ $transaction->cashier->name }}</span>
            </div>
            @if($transaction->customer_name)
                <div class="flex justify-between">
                    <span>Customer:</span>
                    <span>{{ $transaction->customer_name }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="mb-4">
        <table class="w-full text-xs">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2">Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $item)
                    <tr class="border-b">
                        <td class="py-2">{{ $item->product->name }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->price_at_transaction, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="border-t-2 pt-3 mb-4">
        <div class="text-sm space-y-1">
            <div class="flex justify-between">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>
            @if($transaction->discount_amount > 0)
                <div class="flex justify-between text-green-600">
                    <span>Diskon:</span>
                    <span>- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between font-bold text-lg border-t pt-2">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($transaction->final_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-xs text-gray-600">
                <span>Metode Bayar:</span>
                <span class="uppercase">{{ $transaction->payment_method }}</span>
            </div>
        </div>
    </div>

    @if($transaction->notes)
        <div class="mb-4 text-xs text-gray-600">
            <strong>Catatan:</strong> {{ $transaction->notes }}
        </div>
    @endif

    <!-- QR Code -->
    <div class="text-center mb-6">
        <div class="inline-block border-4 border-gray-800 p-3">
            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($transaction->uuid) !!}
        </div>
        <p class="text-xs text-gray-600 mt-2">Scan QR Code di pintu masuk</p>
        <p class="text-xs font-mono text-gray-500">{{ $transaction->uuid }}</p>
    </div>

    <div class="text-center text-xs text-gray-600 border-t pt-4">
        <p>Terima kasih atas kunjungan Anda!</p>
        <p>Selamat menikmati wisata 🌳</p>
        <p class="mt-2 text-gray-500">Simpan struk ini untuk validasi tiket</p>
    </div>

    <!-- Print Button -->
    <div class="mt-6 flex space-x-2 no-print">
        <button 
            onclick="window.print()" 
            class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg"
        >
            🖨️ Cetak Struk
        </button>
        <a 
            href="{{ route('pos.index') }}" 
            class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg text-center"
        >
            ← Kembali ke POS
        </a>
    </div>
</div>

<style>
    @media print {
        .no-print {
            display: none;
        }
        
        body {
            margin: 0;
            padding: 0;
        }
        
        @page {
            size: 80mm auto;
            margin: 0;
        }
    }
</style>
