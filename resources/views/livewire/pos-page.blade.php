<div>
    @section('title', 'POS - Kasir | Cafe')
    
    <!-- Page Header -->
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">Point of Sale</h2>
                <p class="text-sm text-gray-500 mt-1">Kasir: <span class="font-semibold text-gray-700">{{ auth()->user()->name }}</span></p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if (session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-4 rounded-r-lg shadow-sm flex items-start" role="alert">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline ml-1">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-800 px-5 py-4 rounded-r-lg shadow-sm flex items-start" role="alert">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <strong class="font-bold">Sukses!</strong>
                        <span class="block sm:inline ml-1">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 lg:gap-6">
                <!-- KOLOM KIRI: Daftar Produk (2 kolom) -->
                <div class="xl:col-span-2">
                    <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <!-- Clean Header tanpa Background Hijau -->
                        <div class="p-4 lg:p-6 border-b border-gray-100">
                            <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-1 lg:mb-2">Daftar Menu</h3>
                            <p class="text-xs lg:text-sm text-gray-500">Pilih produk untuk ditambahkan ke keranjang belanja</p>
                        </div>

                        <div class="p-4 lg:p-6" wire:loading.class="opacity-50 cursor-wait">
                            <!-- Filter Pills - Interaktif dengan Scale Effect -->
                            <div class="mb-4 lg:mb-6">
                                <div class="flex flex-wrap gap-2 lg:gap-3">
                                    <button 
                                        wire:click="setFilter('all')"
                                        class="px-4 lg:px-6 py-2 lg:py-2.5 rounded-full text-xs lg:text-sm font-bold transition-all duration-300 ease-out {{ $filterType === 'all' ? 'bg-gray-900 text-white shadow-lg scale-105' : 'bg-white text-gray-600 hover:bg-gray-100 hover:text-gray-900 border border-gray-200' }}">
                                        Semua Menu
                                    </button>
                                    <button 
                                        wire:click="setFilter('makanan')"
                                        class="px-4 lg:px-6 py-2 lg:py-2.5 rounded-full text-xs lg:text-sm font-bold transition-all duration-300 ease-out {{ $filterType === 'makanan' ? 'bg-gray-900 text-white shadow-lg scale-105' : 'bg-white text-gray-600 hover:bg-gray-100 hover:text-gray-900 border border-gray-200' }}">
                                        <i class="fas fa-utensils mr-1"></i> Makanan
                                    </button>
                                    <button 
                                        wire:click="setFilter('minuman')"
                                        class="px-4 lg:px-6 py-2 lg:py-2.5 rounded-full text-xs lg:text-sm font-bold transition-all duration-300 ease-out {{ $filterType === 'minuman' ? 'bg-gray-900 text-white shadow-lg scale-105' : 'bg-white text-gray-600 hover:bg-gray-100 hover:text-gray-900 border border-gray-200' }}">
                                        <i class="fas fa-mug-hot mr-1"></i> Minuman
                                    </button>
                                    <button 
                                        wire:click="setFilter('dessert')"
                                        class="px-4 lg:px-6 py-2 lg:py-2.5 rounded-full text-xs lg:text-sm font-bold transition-all duration-300 ease-out {{ $filterType === 'dessert' ? 'bg-gray-900 text-white shadow-lg scale-105' : 'bg-white text-gray-600 hover:bg-gray-100 hover:text-gray-900 border border-gray-200' }}">
                                        <i class="fas fa-ice-cream mr-1"></i> Dessert
                                    </button>
                                </div>
                            </div>

                            <!-- Grid Produk dengan Foto & Info -->
                            @php
                                // Function untuk menentukan foto produk
                                $getProductImage = function($type) {
                                    if ($type === 'makanan') return asset('images/makanan.jpg');
                                    if ($type === 'minuman') return asset('images/minuman.jpg');
                                    if ($type === 'dessert') return asset('images/dessert.jpg');
                                    return asset('images/makanan.jpg');
                                };
                                
                                // Function untuk menentukan theme warna untuk kategori cafe
                                $getProductTheme = function($product) {
                                    $type = $product->type;
                                    
                                    // Makanan: Orange Vibrant
                                    if ($type === 'makanan') {
                                        return [
                                            'text_color' => 'text-orange-600',
                                            'border_hover' => 'hover:border-orange-300',
                                            'ring' => 'group-hover:ring-2 group-hover:ring-orange-200',
                                        ];
                                    }
                                    
                                    // Minuman: Blue Vibrant
                                    if ($type === 'minuman') {
                                        return [
                                            'text_color' => 'text-blue-600',
                                            'border_hover' => 'hover:border-blue-300',
                                            'ring' => 'group-hover:ring-2 group-hover:ring-blue-200',
                                        ];
                                    }
                                    
                                    // Dessert: Pink Vibrant
                                    if ($type === 'dessert') {
                                        return [
                                            'text_color' => 'text-pink-600',
                                            'border_hover' => 'hover:border-pink-300',
                                            'ring' => 'group-hover:ring-2 group-hover:ring-pink-200',
                                        ];
                                    }
                                    
                                    // Default
                                    return [
                                        'text_color' => 'text-amber-600',
                                        'border_hover' => 'hover:border-amber-300',
                                        'ring' => 'group-hover:ring-2 group-hover:ring-amber-200',
                                    ];
                                };
                            @endphp
                            
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-2 xl:grid-cols-3 gap-3 lg:gap-4">
                                @forelse($this->filteredProducts as $product)
                                    @php
                                        $theme = $getProductTheme($product);
                                        $image = $getProductImage($product->type);
                                    @endphp
                                    
                                    <!-- Product Card dengan Foto -->
                                    <button 
                                        wire:click="addToCart({{ $product->id }})"
                                        class="group bg-white border border-gray-100 rounded-xl lg:rounded-2xl shadow-sm {{ $theme['border_hover'] }} {{ $theme['ring'] }} hover:shadow-lg transition-all duration-300 ease-out transform hover:-translate-y-1 active:scale-95 active:duration-100 text-left overflow-hidden">
                                        
                                        <!-- Product Image -->
                                        <div class="relative h-32 lg:h-40 overflow-hidden">
                                            <img src="{{ $image }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                            <!-- Gradient Overlay -->
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                                        </div>
                                        
                                        <!-- Product Info -->
                                        <div class="p-3 lg:p-4 space-y-1 lg:space-y-2">
                                            <!-- Nama Produk -->
                                            <h4 class="font-semibold text-gray-800 text-xs lg:text-sm leading-tight min-h-[2rem] lg:min-h-[2.5rem] line-clamp-2">
                                                {{ $product->name }}
                                            </h4>
                                            
                                            <!-- Harga -->
                                            <p class="{{ $theme['text_color'] }} font-bold text-sm lg:text-base">
                                                Rp {{ number_format($product->price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </button>
                                @empty
                                    <div class="col-span-2 sm:col-span-3 lg:col-span-2 xl:col-span-3 text-center py-8 lg:py-12">
                                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <p class="text-gray-500 font-medium">Tidak ada produk tersedia</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KOLOM KANAN: Keranjang (1 kolom) -->
                <div class="xl:col-span-1">
                    <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border border-gray-100 overflow-hidden xl:sticky xl:top-24">
                        <!-- Header Keranjang -->
                        <div class="p-4 lg:p-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2 lg:space-x-3">
                                    <div class="w-8 h-8 lg:w-10 lg:h-10 bg-amber-600 rounded-lg lg:rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900 text-base lg:text-lg">Current Order</h3>
                                        <p class="text-xs text-gray-500">{{ count($cart) }} item(s)</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 lg:p-6">
                            @if(count($cart) > 0)
                                <!-- List Items dengan Smooth Transitions -->
                                <div class="space-y-3 lg:space-y-4 mb-4 lg:mb-6 max-h-60 xl:max-h-80 overflow-y-auto">
                                    @foreach($cart as $index => $item)
                                        <div class="flex items-start justify-between p-3 lg:p-4 bg-gradient-to-r from-gray-50 to-white hover:from-gray-100 hover:to-gray-50 rounded-lg lg:rounded-xl border border-gray-100 hover:border-gray-200 transition-all duration-300 group">
                                            <div class="flex-1 mr-2 lg:mr-3">
                                                <h4 class="font-semibold text-gray-900 text-xs lg:text-sm mb-1 lg:mb-2 group-hover:text-[#1a4d2e] transition-colors duration-200">{{ $item['product']['name'] }}</h4>
                                                <p class="text-xs text-gray-500 mb-2 lg:mb-3">Rp {{ number_format($item['product']['price'], 0, ',', '.') }} / pcs</p>
                                                
                                                <!-- Quantity Controls dengan Active States -->
                                                <div class="flex items-center space-x-1.5 lg:space-x-2">
                                                    <button 
                                                        wire:click="decreaseQuantity({{ $index }})"
                                                        class="w-7 h-7 lg:w-8 lg:h-8 rounded-lg bg-white border border-gray-200 hover:border-[#1a4d2e] text-gray-600 hover:text-[#1a4d2e] font-bold transition-all duration-200 flex items-center justify-center active:scale-90 active:duration-100">
                                                        <svg class="w-3 h-3 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                        </svg>
                                                    </button>
                                                    <input 
                                                        type="number" 
                                                        wire:model.blur="cart.{{ $index }}.quantity"
                                                        wire:change="updateTotal"
                                                        class="w-12 lg:w-16 text-center border border-gray-200 rounded-lg py-1 lg:py-1.5 text-xs lg:text-sm font-semibold text-gray-900 focus:border-[#1a4d2e] focus:ring-2 focus:ring-green-500/20 transition-all duration-200"
                                                        min="1"
                                                    >
                                                    <button 
                                                        wire:click="increaseQuantity({{ $index }})"
                                                        class="w-7 h-7 lg:w-8 lg:h-8 rounded-lg bg-white border border-gray-200 hover:border-[#1a4d2e] text-gray-600 hover:text-[#1a4d2e] font-bold transition-all duration-200 flex items-center justify-center active:scale-90 active:duration-100">
                                                        <svg class="w-3 h-3 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- Price & Remove dengan Better Hover Effect -->
                                            <div class="text-right">
                                                <p class="font-bold text-[#1a4d2e] text-xs lg:text-sm mb-1 lg:mb-2">
                                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                                </p>
                                                <button 
                                                    wire:click="removeFromCart({{ $index }})"
                                                    class="text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg p-1 lg:p-1.5 transition-all duration-200 active:scale-90 active:duration-100">
                                                    <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Total Section -->
                                <div class="bg-green-50 rounded-lg lg:rounded-xl p-4 lg:p-5 mb-4 lg:mb-6 space-y-2 lg:space-y-3">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-600 font-medium">Subtotal</span>
                                        <span class="font-semibold text-gray-900">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                                    </div>
                                    
                                    @if($discountAmount > 0)
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-green-700 font-medium">Diskon (20%)</span>
                                            <span class="font-semibold text-green-700">- Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="pt-3 border-t border-green-200">
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-700 font-bold text-base">Total Bayar</span>
                                                <span class="font-extrabold text-[#1a4d2e] text-2xl">Rp {{ number_format($finalAmount, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="pt-3 border-t border-green-200">
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-700 font-bold text-base">Total Bayar</span>
                                                <span class="font-extrabold text-[#1a4d2e] text-2xl">Rp {{ number_format($finalAmount, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Payment Method Selection dengan Smooth Transitions -->
                                <div class="mb-4 lg:mb-6">
                                    <label class="block text-xs lg:text-sm font-semibold text-gray-700 mb-2 lg:mb-3">Metode Pembayaran:</label>
                                    <div class="grid grid-cols-2 gap-2 lg:gap-3">
                                        <button 
                                            type="button"
                                            wire:click="$set('paymentMethod', 'cash')"
                                            class="p-3 lg:p-4 rounded-lg lg:rounded-xl border-2 transition-all duration-300 active:scale-95 active:duration-100 {{ $paymentMethod === 'cash' ? 'border-[#1a4d2e] bg-green-50 shadow-md shadow-green-100' : 'border-gray-200 bg-white hover:border-gray-300 hover:shadow-md' }}">
                                            <div class="flex flex-col items-center space-y-1.5 lg:space-y-2">
                                                <svg class="w-6 h-6 lg:w-8 lg:h-8 {{ $paymentMethod === 'cash' ? 'text-[#1a4d2e]' : 'text-gray-400' }} transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                <span class="font-semibold text-xs lg:text-sm {{ $paymentMethod === 'cash' ? 'text-[#1a4d2e]' : 'text-gray-600' }} transition-colors duration-300">Cash</span>
                                            </div>
                                        </button>
                                        <button 
                                            type="button"
                                            wire:click="$set('paymentMethod', 'qris')"
                                            class="p-3 lg:p-4 rounded-lg lg:rounded-xl border-2 transition-all duration-300 active:scale-95 active:duration-100 {{ $paymentMethod === 'qris' ? 'border-[#1a4d2e] bg-green-50 shadow-md shadow-green-100' : 'border-gray-200 bg-white hover:border-gray-300 hover:shadow-md' }}">
                                            <div class="flex flex-col items-center space-y-1.5 lg:space-y-2">
                                                <svg class="w-6 h-6 lg:w-8 lg:h-8 {{ $paymentMethod === 'qris' ? 'text-[#1a4d2e]' : 'text-gray-400' }} transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                                </svg>
                                                <span class="font-semibold text-xs lg:text-sm {{ $paymentMethod === 'qris' ? 'text-[#1a4d2e]' : 'text-gray-600' }} transition-colors duration-300">QRIS</span>
                                            </div>
                                        </button>
                                    </div>
                                </div>

                                <!-- Action Buttons dengan Loading States -->
                                <div class="space-y-2 lg:space-y-3">
                                    <!-- Tombol Bayar dengan Gradient Orange ke Red (High Contrast) dan Loading State -->
                                    <button 
                                        wire:click="checkout"
                                        wire:loading.attr="disabled"
                                        wire:loading.class="opacity-75 cursor-wait"
                                        class="w-full bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 disabled:from-gray-400 disabled:to-gray-500 text-white font-bold py-3 lg:py-4 rounded-lg lg:rounded-xl shadow-lg shadow-orange-500/40 hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02] active:scale-95 active:duration-100 relative overflow-hidden group">
                                        <!-- Button Content -->
                                        <div class="flex items-center justify-center space-x-2">
                                            <!-- Loading Spinner -->
                                            <svg wire:loading wire:target="checkout" class="animate-spin h-4 w-4 lg:h-5 lg:w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <i wire:loading.remove wire:target="checkout" class="fa-solid fa-check-circle text-lg lg:text-xl"></i>
                                            <span class="text-sm lg:text-lg">
                                                <span wire:loading.remove wire:target="checkout">Proses Pembayaran</span>
                                                <span wire:loading wire:target="checkout">Memproses...</span>
                                            </span>
                                        </div>
                                    </button>

                                    <!-- Clear Cart Button dengan Smooth Transition -->
                                    <button 
                                        wire:click="clearCart"
                                        wire:confirm="Yakin ingin mengosongkan keranjang?"
                                        class="w-full bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-2.5 lg:py-3 rounded-lg lg:rounded-xl transition-all duration-300 border border-red-200 hover:border-red-300 active:scale-95 active:duration-100">
                                        <div class="flex items-center justify-center space-x-2">
                                            <i class="fa-solid fa-trash-can text-sm lg:text-base"></i>
                                            <span class="text-xs lg:text-sm">Kosongkan Keranjang</span>
                                        </div>
                                    </button>
                                </div>

                            @else
                                <!-- Empty State -->
                                <div class="text-center py-8 lg:py-12">
                                    <svg class="w-16 h-16 lg:w-20 lg:h-20 mx-auto text-gray-300 mb-3 lg:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p class="text-gray-500 font-medium mb-2">Keranjang Kosong</p>
                                    <p class="text-xs text-gray-400">Pilih produk untuk memulai transaksi</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
