<x-app-layout>
    @section('title', 'Dashboard - POS Kasir Cafe')
    
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">Dashboard</h2>
            <p class="text-sm text-gray-500 mt-1">Selamat Datang, <span class="font-semibold text-gray-700">{{ auth()->user()->name }}</span> <i class="fas fa-hand-wave text-amber-600"></i></p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Real-Time Statistics Cards -->
            <livewire:dashboard-stats />

            <!-- Quick Access Menu -->
            <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-6 mb-6 lg:mb-8">
                <h3 class="text-base lg:text-lg font-bold text-gray-900 mb-2">Akses Cepat</h3>
                <p class="text-xs lg:text-sm text-gray-500 mb-4 lg:mb-6">Role: <span class="inline-flex items-center px-2 lg:px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">{{ ucfirst(auth()->user()->role) }}</span></p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-4">
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="group bg-gradient-to-br from-amber-600 to-orange-700 text-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition-all transform hover:scale-105">
                            <div class="flex items-center mb-3">
                                <div class="bg-white/20 p-3 rounded-xl mr-3">
                                    <i class="fa-solid fa-chart-line text-xl"></i>
                                </div>
                                <h4 class="text-lg font-bold">Dashboard Admin</h4>
                            </div>
                            <p class="text-sm text-white/80">Lihat laporan dan statistik lengkap</p>
                        </a>
                    @endif

                    @if(auth()->user()->role === 'kasir' || auth()->user()->role === 'admin')
                        <a href="{{ route('pos.index') }}" class="group bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition-all transform hover:scale-105">
                            <div class="flex items-center mb-3">
                                <div class="bg-white/20 p-3 rounded-xl mr-3">
                                    <i class="fa-solid fa-cash-register text-xl"></i>
                                </div>
                                <h4 class="text-lg font-bold">POS - Kasir</h4>
                            </div>
                            <p class="text-sm text-white/80">Proses transaksi penjualan cafe</p>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Chart & Recent Transactions Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6 mb-6 lg:mb-8">
                <!-- Chart Section (2/3 Width) -->
                <div class="lg:col-span-2">
                    <livewire:dashboard-chart />
                </div>

                <!-- Recent Transactions Section (1/3 Width) -->
                <div class="lg:col-span-1">
                    <livewire:recent-transactions />
                </div>
            </div>

            <!-- Information Box -->
            <div class="bg-gradient-to-r from-orange-50 to-amber-50 border border-orange-200 rounded-xl lg:rounded-2xl p-4 lg:p-6">
                <div class="flex items-start">
                    <div class="bg-orange-100 p-2 lg:p-3 rounded-lg lg:rounded-xl mr-3 lg:mr-4">
                        <i class="fa-solid fa-circle-info text-lg lg:text-xl text-orange-700"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-orange-900 mb-2 lg:mb-3 text-base lg:text-lg">Informasi Sistem</h4>
                        <ul class="space-y-1.5 lg:space-y-2 text-xs lg:text-sm text-orange-800">
                            <li class="flex items-start">
                                <i class="fa-solid fa-check-circle text-orange-600 mr-2 mt-0.5"></i>
                                <span>Sistem POS Kasir untuk Cafe</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fa-solid fa-check-circle text-orange-600 mr-2 mt-0.5"></i>
                                <span>Kategori produk: Makanan, Minuman, dan Dessert</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fa-solid fa-check-circle text-orange-600 mr-2 mt-0.5"></i>
                                <span>Gunakan menu navigasi di samping untuk mengakses fitur sesuai role Anda</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
