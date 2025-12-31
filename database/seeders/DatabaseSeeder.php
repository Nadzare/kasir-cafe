<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ========================================
        // 1. SEEDER USER DEFAULT
        // ========================================
        $users = [
            [
                'name' => 'Admin Tuksirah',
                'email' => 'admin@tuksirah.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Kasir Tuksirah',
                'email' => 'kasir@tuksirah.com',
                'password' => Hash::make('password'),
                'role' => 'kasir',
            ],
            [
                'name' => 'Gatekeeper Tuksirah',
                'email' => 'gate@tuksirah.com',
                'password' => Hash::make('password'),
                'role' => 'gatekeeper',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        $this->command->info('✅ Users created successfully!');

        // ========================================
        // 2. SEEDER MASTER PRODUK DEFAULT
        // ========================================
        $products = [
            [
                'name' => 'Tiket Masuk',
                'type' => 'ticket',
                'price' => 10000,
                'is_stock_managed' => false,
                'description' => 'Tiket masuk wisata Tuksirah Kali Pemali',
            ],
            [
                'name' => 'Parkir Motor',
                'type' => 'parking',
                'price' => 3000,
                'is_stock_managed' => false,
                'description' => 'Parkir kendaraan roda dua',
            ],
            [
                'name' => 'Parkir Mobil',
                'type' => 'parking',
                'price' => 5000,
                'is_stock_managed' => false,
                'description' => 'Parkir kendaraan roda empat',
            ],
            [
                'name' => 'Sewa Aula',
                'type' => 'facility',
                'price' => 150000,
                'is_stock_managed' => false,
                'description' => 'Sewa aula untuk acara',
            ],
            [
                'name' => 'Sound System',
                'type' => 'addon',
                'price' => 50000,
                'is_stock_managed' => false,
                'description' => 'Sewa sound system untuk acara',
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        $this->command->info('✅ Products created successfully!');
        $this->command->info('');
        $this->command->info('=======================================');
        $this->command->info('📝 DEFAULT LOGIN CREDENTIALS:');
        $this->command->info('=======================================');
        $this->command->info('👤 Admin     : admin@tuksirah.com');
        $this->command->info('👤 Kasir     : kasir@tuksirah.com');
        $this->command->info('👤 Gatekeeper: gate@tuksirah.com');
        $this->command->info('🔒 Password  : password');
        $this->command->info('=======================================');
    }
}
