<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi #{{ $transaction->id }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Styling untuk Thermal Printer (58mm - 80mm) */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Courier New', monospace;
            background-color: #f3f4f6;
        }
        
        .receipt-container {
            max-width: 350px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .dashed-line {
            border-top: 2px dashed #333;
            margin: 10px 0;
        }
        
        .dotted-line {
            border-top: 1px dotted #999;
            margin: 8px 0;
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
            }
            
            .receipt-container {
                max-width: 80mm; /* Thermal printer width */
                width: 80mm;
                margin: 0;
                padding: 10px;
                box-shadow: none;
            }
            
            /* Hide print button */
            .no-print {
                display: none !important;
            }
            
            /* Optimize QR Code for print */
            .qr-code {
                image-rendering: pixelated;
                image-rendering: -moz-crisp-edges;
                image-rendering: crisp-edges;
            }
            
            /* Page setup for thermal printer */
            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
        
        /* Smaller width for 58mm printer */
        @media print and (max-width: 58mm) {
            .receipt-container {
                max-width: 58mm;
                width: 58mm;
            }
            
            @page {
                size: 58mm auto;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- HEADER -->
        <div class="text-center mb-4">
            <h1 class="text-xl font-bold mb-1">TUKSIRAH KALI PEMALI</h1>
            <p class="text-sm">Wisata Alam & Rekreasi</p>
            <p class="text-xs text-gray-600">Jl. Raya Tuksirah, Kec. Pemali</p>
            <p class="text-xs text-gray-600">Telp: (0281) 123-4567</p>
        </div>
        
        <div class="dashed-line"></div>
        
        <!-- TRANSACTION INFO -->
        <div class="text-xs space-y-1 mb-3">
            <div class="flex justify-between">
                <span>No. Transaksi:</span>
                <span class="font-bold">#{{ $transaction->id }}</span>
            </div>
            <div class="flex justify-between">
                <span>UUID:</span>
                <span class="font-mono text-[10px]">{{ substr($transaction->uuid, 0, 18) }}...</span>
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
            <div class="flex justify-between">
                <span>Pembayaran:</span>
                <span class="uppercase font-semibold">{{ $transaction->payment_method }}</span>
            </div>
        </div>
        
        <div class="dashed-line"></div>
        
        <!-- ITEMS TABLE -->
        <div class="mb-3">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-gray-400">
                        <th class="text-left py-1">Item</th>
                        <th class="text-center py-1">Qty</th>
                        <th class="text-right py-1">Harga</th>
                        <th class="text-right py-1">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->items as $item)
                        <tr class="border-b border-dotted border-gray-300">
                            <td class="py-2 pr-2">{{ $item->product->name }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">{{ number_format($item->price_at_transaction, 0) }}</td>
                            <td class="text-right font-semibold">{{ number_format($item->subtotal, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="dashed-line"></div>
        
        <!-- SUMMARY -->
        <div class="text-sm space-y-2 mb-3">
            <div class="flex justify-between">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>
            
            @if($transaction->discount_amount > 0)
                <div class="flex justify-between text-green-600 font-semibold">
                    <span>Diskon Rombongan:</span>
                    <span>- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            
            <div class="dotted-line"></div>
            
            <div class="flex justify-between text-lg font-bold">
                <span>TOTAL BAYAR:</span>
                <span>Rp {{ number_format($transaction->final_amount, 0, ',', '.') }}</span>
            </div>
        </div>
        
        @if($transaction->notes)
            <div class="dashed-line"></div>
            <div class="text-xs mb-3">
                <strong>Catatan:</strong> {{ $transaction->notes }}
            </div>
        @endif
        
        <div class="dashed-line"></div>
        
        <!-- QR CODE -->
        <div class="text-center my-4">
            <div class="inline-block border-4 border-gray-800 p-2 qr-code">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->margin(1)->generate($transaction->uuid) !!}
            </div>
            <p class="text-xs text-gray-600 mt-2">Scan QR Code di Pintu Masuk</p>
            <p class="text-[10px] font-mono text-gray-500 break-all mt-1">{{ $transaction->uuid }}</p>
        </div>
        
        <div class="dashed-line"></div>
        
        <!-- FOOTER -->
        <div class="text-center text-xs text-gray-700 space-y-1">
            <p class="font-bold">Terima Kasih Atas Kunjungan Anda!</p>
            <p>🌳 Selamat Menikmati Wisata 🌳</p>
            <p class="text-[10px] text-gray-500 mt-3">Simpan struk ini untuk validasi tiket</p>
            <p class="text-[10px] text-gray-500">{{ $transaction->created_at->format('d M Y, H:i:s') }}</p>
        </div>
        
        <div class="dashed-line"></div>
        
        <!-- ACTION BUTTONS (Hidden on Print) -->
        <div class="no-print mt-6 space-y-2">
            <button 
                onclick="window.print()" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition duration-200 flex items-center justify-center"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                🖨️ Cetak Struk
            </button>
            
            <a 
                href="{{ route('pos.index') }}" 
                class="block w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg text-center transition duration-200"
            >
                ← Kembali ke Kasir (POS)
            </a>
            
            <a 
                href="{{ route('dashboard') }}" 
                class="block w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg text-center transition duration-200"
            >
                🏠 Ke Dashboard
            </a>
        </div>
    </div>
    
    <!-- Auto Print Script -->
    <script>
        // Auto-print when page loads (optional - uncomment if needed)
        // window.addEventListener('load', function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 500); // Delay 500ms untuk memastikan QR code ter-render
        // });
        
        // Keyboard shortcut: Ctrl+P untuk print
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
        
        // Detect after print
        window.addEventListener('afterprint', function() {
            console.log('Print completed or cancelled');
            // Optional: Auto redirect after print
            // setTimeout(function() {
            //     window.location.href = "{{ route('pos.index') }}";
            // }, 1000);
        });
    </script>
</body>
</html>
