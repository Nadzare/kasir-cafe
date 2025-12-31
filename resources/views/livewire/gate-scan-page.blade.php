<div>
    @section('title', 'Scanner Gate | Wisata Tuksirah')
    
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-gray-900 leading-tight flex items-center">
                <i class="fa-solid fa-qrcode mr-3 text-[#1a4d2e]"></i>
                Scanner Tiket Gate
            </h2>
            <p class="text-sm text-gray-500 mt-1">Validasi tiket pengunjung masuk wisata</p>
        </div>
    </x-slot>

    <style>
        @media (max-width: 640px) {
            body {
                overflow-x: hidden;
            }
        }
        
        .scan-animation {
            animation: scanLine 2s ease-in-out infinite;
        }
        
        @keyframes scanLine {
            0%, 100% { transform: translateY(-100%); }
            50% { transform: translateY(100%); }
        }
        
        .slide-up {
            animation: slideUp 0.5s ease-out forwards;
        }
        
        @keyframes slideUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes pulse-slow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        .animate-pulse-slow {
            animation: pulse-slow 2s ease-in-out infinite;
        }
        
        /* Scanner styling */
        #qr-reader {
            border: 4px solid #3b82f6;
        }
        
        #qr-reader video {
            border-radius: 0.5rem;
        }
    </style>

    <div class="py-4 lg:py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if($scanStatus === null)
            <!-- READY STATE: Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6 mb-4 lg:mb-6">
                <!-- Camera View Column (2/3) -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl lg:rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="p-3 lg:p-4 bg-gradient-to-r from-[#1a4d2e] to-[#2d7a4f] text-white flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="relative flex h-2 w-2 lg:h-3 lg:w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 lg:h-3 lg:w-3 bg-white"></span>
                                </div>
                                <span class="font-bold text-xs lg:text-sm">KAMERA AKTIF</span>
                            </div>
                            <i class="fa-solid fa-camera text-lg lg:text-xl"></i>
                        </div>
                        <div class="relative bg-gray-900" style="aspect-ratio: 4/3;">
                            <div id="qr-reader" class="w-full h-full"></div>
                            
                            <!-- Scan Overlay -->
                            <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                                <div class="w-40 h-40 sm:w-48 sm:h-48 lg:w-56 lg:h-56 border-2 lg:border-4 border-green-400 rounded-xl lg:rounded-2xl relative">
                                    <div class="scan-animation absolute inset-0 bg-gradient-to-b from-transparent via-green-400/20 to-transparent"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instructions & Manual Input Column (1/3) -->
                <div class="space-y-4 lg:space-y-6">
                    <!-- Instructions Card -->
                    <div class="bg-blue-50 rounded-xl lg:rounded-2xl p-4 lg:p-6 border border-blue-200">
                        <h3 class="text-blue-900 font-bold text-base lg:text-lg mb-3 lg:mb-4 flex items-center">
                            <i class="fa-solid fa-info-circle mr-2"></i>
                            Cara Scan
                        </h3>
                        <ul class="space-y-2 lg:space-y-3 text-blue-800 text-xs lg:text-sm">
                            <li class="flex items-start">
                                <div class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                    <span class="text-xs font-bold">1</span>
                                </div>
                                <span>Arahkan QR Code ke area hijau di tengah kamera</span>
                            </li>
                            <li class="flex items-start">
                                <div class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                    <span class="text-xs font-bold">2</span>
                                </div>
                                <span>Pastikan pencahayaan cukup terang</span>
                            </li>
                            <li class="flex items-start">
                                <div class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                    <span class="text-xs font-bold">3</span>
                                </div>
                                <span>Tunggu hingga scan otomatis berhasil</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Manual Input Card -->
                    <div class="bg-white rounded-xl lg:rounded-2xl p-4 lg:p-6 shadow-sm border border-gray-200">
                        <h4 class="text-gray-900 font-bold text-base lg:text-lg mb-3 lg:mb-4 flex items-center">
                            <i class="fa-solid fa-keyboard mr-2 text-gray-400"></i>
                            Input Manual
                        </h4>
                        <form wire:submit.prevent="handleScan($refs.manualInput.value)" class="space-y-2 lg:space-y-3">
                            <input 
                                x-ref="manualInput"
                                type="text" 
                                class="w-full px-3 lg:px-4 py-2 lg:py-3 border border-gray-300 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-[#1a4d2e] focus:border-transparent placeholder-gray-400 text-xs lg:text-sm"
                                placeholder="Paste UUID tiket di sini..."
                            >
                            <button 
                                type="submit"
                                class="w-full bg-gradient-to-r from-[#1a4d2e] to-[#2d7a4f] hover:from-[#2d7a4f] hover:to-[#1a4d2e] text-white font-bold py-2.5 lg:py-3 rounded-lg lg:rounded-xl transition-all shadow-lg text-sm lg:text-base">
                                <i class="fa-solid fa-magnifying-glass mr-2"></i>
                                Cek Tiket
                            </button>
                        </form>
                    </div>
                </div>
            </div>
                    
            @elseif($scanStatus === 'success')
            <!-- SUCCESS STATE: Bottom Sheet Style -->
            <div class="fixed inset-0 bg-black/90 flex items-end justify-center p-3 lg:p-4 z-50 slide-up">
                <div class="bg-gradient-to-br from-green-500 to-green-600 w-full max-w-2xl rounded-t-2xl lg:rounded-t-3xl shadow-2xl p-6 lg:p-8">
                    <div class="text-center text-white">
                        <div class="inline-flex items-center justify-center w-16 h-16 lg:w-24 lg:h-24 bg-white rounded-full mb-4 lg:mb-6 animate-bounce">
                            <i class="fa-solid fa-circle-check text-4xl lg:text-6xl text-green-500"></i>
                        </div>
                        
                        <h2 class="text-2xl lg:text-4xl font-bold mb-2">{{ $scanMessage }}</h2>
                        <p class="text-lg lg:text-2xl mb-6 lg:mb-8 text-green-100">Selamat Datang!</p>
                        
                        @if($transactionData)
                            <div class="bg-white/20 backdrop-blur-lg rounded-xl lg:rounded-2xl p-4 lg:p-6 mb-4 lg:mb-6 text-left">
                                <div class="grid grid-cols-2 gap-3 lg:gap-4">
                                    <div>
                                        <p class="text-sm text-green-100 mb-1">Transaksi ID</p>
                                        <p class="text-xl font-bold">#{{ $transactionData['id'] }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-green-100 mb-1">Nama Tamu</p>
                                        <p class="text-xl font-bold">{{ $transactionData['customer_name'] }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-green-100 mb-1">Jumlah Pax</p>
                                        <p class="text-2xl font-bold">{{ $transactionData['total_pax'] }} <i class="fa-solid fa-users text-lg"></i></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-green-100 mb-1">Total Bayar</p>
                                        <p class="text-xl font-bold">Rp {{ number_format($transactionData['total_amount'], 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <button 
                            wire:click="resetScanner"
                            class="w-full bg-white text-green-600 font-bold text-base lg:text-xl py-4 lg:py-5 rounded-xl lg:rounded-2xl shadow-xl hover:shadow-2xl transition-all transform hover:scale-105">
                            <i class="fa-solid fa-arrow-right mr-2"></i>
                            Scan Tiket Berikutnya
                        </button>
                    </div>
                </div>
            </div>
                    
            @elseif($scanStatus === 'already_used')
                        <!-- ALREADY USED STATE: Bottom Sheet -->
                        <div class="fixed inset-0 bg-black/90 flex items-end justify-center p-3 lg:p-4 z-50 slide-up">
                            <div class="bg-gradient-to-br from-red-500 to-red-600 w-full max-w-2xl rounded-t-2xl lg:rounded-t-3xl shadow-2xl p-6 lg:p-8">
                                <div class="text-center text-white">
                                    <div class="inline-flex items-center justify-center w-16 h-16 lg:w-24 lg:h-24 bg-white rounded-full mb-4 lg:mb-6">
                                        <i class="fa-solid fa-circle-xmark text-4xl lg:text-6xl text-red-500 animate-pulse"></i>
                                    </div>
                                    
                                    <h2 class="text-2xl lg:text-4xl font-bold mb-2">{{ $scanMessage }}</h2>
                                    <p class="text-base lg:text-xl mb-4 lg:mb-6 text-red-100">Tiket Tidak Valid</p>
                                    
                                    @if($transactionData)
                                        <div class="bg-white/20 backdrop-blur-lg rounded-xl lg:rounded-2xl p-4 lg:p-6 mb-3 lg:mb-4">
                                            <div class="space-y-2 lg:space-y-3">
                                                <div>
                                                    <p class="text-sm text-red-100">Check-in Pertama</p>
                                                    <p class="text-2xl font-bold">{{ $validationTime }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-red-100">Customer</p>
                                                    <p class="text-xl font-bold">{{ $transactionData['customer_name'] }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-red-100">Di-scan oleh</p>
                                                    <p class="text-xl font-bold">{{ $transactionData['scanned_by'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="bg-yellow-400 text-yellow-900 px-4 lg:px-6 py-3 lg:py-4 rounded-lg lg:rounded-xl mb-4 lg:mb-6 font-bold text-sm lg:text-lg">
                                        <i class="fa-solid fa-triangle-exclamation mr-2"></i>
                                        PERINGATAN: Tiket sudah pernah digunakan!
                                    </div>
                                    
                                    <button 
                                        wire:click="resetScanner"
                                        class="w-full bg-white text-red-600 font-bold text-base lg:text-xl py-4 lg:py-5 rounded-xl lg:rounded-2xl shadow-xl hover:shadow-2xl transition-all">
                                        <i class="fa-solid fa-arrow-right mr-2"></i>
                                        Scan Tiket Berikutnya
                                    </button>
                                </div>
                            </div>
                        </div>
                    
            @elseif($scanStatus === 'error')
                        <!-- ERROR STATE: Bottom Sheet -->
                        <div class="fixed inset-0 bg-black/90 flex items-end justify-center p-3 lg:p-4 z-50 slide-up">
                            <div class="bg-gradient-to-br from-orange-500 to-orange-600 w-full max-w-2xl rounded-t-2xl lg:rounded-t-3xl shadow-2xl p-6 lg:p-8">
                                <div class="text-center text-white">
                                    <div class="inline-flex items-center justify-center w-16 h-16 lg:w-24 lg:h-24 bg-white rounded-full mb-4 lg:mb-6">
                                        <i class="fa-solid fa-exclamation-triangle text-4xl lg:text-6xl text-orange-500"></i>
                                    </div>
                                    
                                    <h2 class="text-2xl lg:text-4xl font-bold mb-2">Tiket Tidak Ditemukan</h2>
                                    <p class="text-base lg:text-xl mb-4 lg:mb-6 text-orange-100">{{ $scanMessage }}</p>
                                    
                                    <button 
                                        wire:click="resetScanner"
                                        class="w-full bg-white text-orange-600 font-bold text-base lg:text-xl py-4 lg:py-5 rounded-xl lg:rounded-2xl shadow-xl hover:shadow-2xl transition-all">
                                        <i class="fa-solid fa-arrow-right mr-2"></i>
                                        Coba Scan Lagi
                                    </button>
                                </div>
                            </div>
                        </div>
            @endif

            <!-- Statistics Today -->
            <div class="mt-4 lg:mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-4 lg:p-6 rounded-xl lg:rounded-2xl shadow-xl">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-white/80 text-sm font-medium">Tiket Ter-scan</span>
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="fa-solid fa-ticket text-lg"></i>
                        </div>
                    </div>
                    <div class="text-4xl font-bold">{{ $this->todayStats['total_scanned'] }}</div>
                    <div class="text-xs text-white/70 mt-1">Hari Ini</div>
                </div>
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-2xl shadow-xl">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-white/80 text-sm font-medium">Total Transaksi</span>
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
                        </div>
                    </div>
                    <div class="text-4xl font-bold">{{ $this->todayStats['total_transactions'] }}</div>
                    <div class="text-xs text-white/70 mt-1">Hari Ini</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let html5QrCode = null;
            let isProcessing = false;
            
            // Initialize QR Code Scanner
            function startScanner() {
                if (html5QrCode) {
                    html5QrCode.clear();
                }
                
                html5QrCode = new Html5Qrcode("qr-reader");
                
                const config = {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0,
                };
                
                html5QrCode.start(
                    { facingMode: "environment" }, // Use back camera
                    config,
                    (decodedText, decodedResult) => {
                        if (!isProcessing) {
                            isProcessing = true;
                            console.log("QR Code detected:", decodedText);
                            
                            // Call Livewire method
                            @this.call('handleScan', decodedText);
                            
                            // Reset processing flag after 2 seconds
                            setTimeout(() => {
                                isProcessing = false;
                            }, 2000);
                        }
                    },
                    (errorMessage) => {
                        // Scan error (dapat diabaikan jika bukan error kritis)
                    }
                ).catch((err) => {
                    console.error("Camera error:", err);
                    alert("Tidak dapat mengakses kamera. Pastikan izin kamera sudah diberikan.");
                });
            }
            
            // Stop Scanner
            function stopScanner() {
                if (html5QrCode) {
                    html5QrCode.stop().then(() => {
                        console.log("Scanner stopped");
                    }).catch((err) => {
                        console.error("Error stopping scanner:", err);
                    });
                }
            }
            
            // Initialize on page load
            startScanner();
            
            // Livewire Events
            Livewire.on('stop-scanner', () => {
                stopScanner();
            });
            
            Livewire.on('restart-scanner', () => {
                setTimeout(() => {
                    startScanner();
                }, 500);
            });
            
            // Auto reset after 5 seconds on success
            Livewire.on('auto-reset', () => {
                setTimeout(() => {
                    @this.call('resetScanner');
                }, 5000);
            });
            
            // Play Sound
            Livewire.on('play-sound', (event) => {
                const type = event.type;
                
                // Create audio context for beep sound
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                if (type === 'success') {
                    // Success beep (high pitch, short)
                    oscillator.frequency.value = 1000;
                    gainNode.gain.value = 0.3;
                    oscillator.start();
                    oscillator.stop(audioContext.currentTime + 0.2);
                } else {
                    // Error beep (low pitch, long)
                    oscillator.frequency.value = 400;
                    gainNode.gain.value = 0.3;
                    oscillator.start();
                    oscillator.stop(audioContext.currentTime + 0.5);
                }
            });
            
            // Cleanup on page unload
            window.addEventListener('beforeunload', () => {
                stopScanner();
            });
        });
    </script>
</div>
