<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi #{{ $transaction->id }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            border-radius: 8px;
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
                border-radius: 0;
            }
            
            /* Hide print button */
            .no-print {
                display: none !important;
            }
            
            /* Print background colors */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
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
        <div class="text-center mb-4 pb-2">
            <div class="flex justify-center mb-3">
                <img src="{{ asset('images/kndlogo.png') }}" alt="K&D Coffee" class="h-20 w-auto">
            </div>
            <h1 class="text-2xl font-bold mb-2 tracking-wide">K&D COFFEE</h1>
            <p class="text-sm font-medium mb-1">Cafe & Resto</p>
            <div class="text-xs text-gray-600 space-y-0.5">
                <p>Jl. Raya Cafe No. 123, Kota</p>
                <p>Telp: (0281) 999-8888</p>
            </div>
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
        <div class="text-sm space-y-2 mb-4">
            <div class="flex justify-between text-gray-700">
                <span>Subtotal:</span>
                <span class="font-medium">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>
            
            @if($transaction->discount_amount > 0)
                <div class="flex justify-between text-green-600 font-semibold">
                    <span>Diskon:</span>
                    <span>- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            
            <div class="dotted-line"></div>
            
            <div class="flex justify-between text-base font-bold bg-gray-100 px-3 py-2 rounded mt-2">
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
        
        <!-- FOOTER -->
        <div class="text-center text-xs text-gray-700 space-y-2 py-2">
            <p class="font-bold text-base">Terima Kasih!</p>
            <p class="text-sm">Selamat Menikmati Hidangan</p>
            <p class="text-xs font-semibold mt-2">~ K&D Coffee ~</p>
            <p class="text-[10px] text-gray-500 mt-3">Simpan struk ini untuk transaksi Anda</p>
            <p class="text-[10px] text-gray-500">{{ $transaction->created_at->format('d M Y, H:i:s') }}</p>
        </div>
        
        <div class="dashed-line"></div>
        
        <!-- ACTION BUTTONS (Hidden on Print) -->
        <div class="no-print mt-6 space-y-3">
            <button 
                onclick="window.print()" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition duration-200 flex items-center justify-center"
            >
                <i class="fas fa-print mr-2"></i>
                Cetak Struk
            </button>
            
            <a 
                href="{{ route('pos.index') }}" 
                class="block w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg text-center transition duration-200 flex items-center justify-center"
            >
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Kasir (POS)
            </a>
            
            <a 
                href="{{ route('dashboard') }}" 
                class="block w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg text-center transition duration-200 flex items-center justify-center"
            >
                <i class="fas fa-home mr-2"></i>
                Ke Dashboard
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
