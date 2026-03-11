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
                'name' => 'Admin Cafe',
                'email' => 'admin@cafe.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Kasir Cafe',
                'email' => 'kasir@cafe.com',
                'password' => Hash::make('password'),
                'role' => 'kasir',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        $this->command->info('✅ Users created successfully!');

        // ========================================
        // 2. SEEDER MASTER PRODUK CAFE
        // ========================================
        $products = [
            // === MAKANAN ===
            [
                'name' => 'Nasi Goreng',
                'type' => 'makanan',
                'price' => 25000,
                'is_stock_managed' => false,
                'description' => 'Nasi goreng spesial dengan telur dan ayam',
            ],
            [
                'name' => 'Nasi Kebuli',
                'type' => 'makanan',
                'price' => 35000,
                'is_stock_managed' => false,
                'description' => 'Nasi kebuli kaya rempah dengan daging kambing/sapi',
            ],
            [
                'name' => 'Rice Bowl - Chicken Katsu',
                'type' => 'makanan',
                'price' => 30000,
                'is_stock_managed' => false,
                'description' => 'Rice bowl dengan chicken katsu crispy',
            ],
            [
                'name' => 'Rice Bowl - Beef Teriyaki',
                'type' => 'makanan',
                'price' => 35000,
                'is_stock_managed' => false,
                'description' => 'Rice bowl dengan daging sapi teriyaki',
            ],
            [
                'name' => 'Rice Bowl - Sambal Matah',
                'type' => 'makanan',
                'price' => 32000,
                'is_stock_managed' => false,
                'description' => 'Rice bowl dengan sambal matah khas Bali',
            ],
            
            // === MINUMAN ===
            [
                'name' => 'Air Putih',
                'type' => 'minuman',
                'price' => 5000,
                'is_stock_managed' => false,
                'description' => 'Air mineral kemasan',
            ],
            [
                'name' => 'Es Teh',
                'type' => 'minuman',
                'price' => 8000,
                'is_stock_managed' => false,
                'description' => 'Es teh manis segar',
            ],
            [
                'name' => 'Vanilla Latte',
                'type' => 'minuman',
                'price' => 25000,
                'is_stock_managed' => false,
                'description' => 'Kopi latte dengan vanilla cream',
            ],
            [
                'name' => 'Matcha Latte',
                'type' => 'minuman',
                'price' => 28000,
                'is_stock_managed' => false,
                'description' => 'Teh hijau matcha dengan susu',
            ],
            [
                'name' => 'Redvelvet Latte',
                'type' => 'minuman',
                'price' => 28000,
                'is_stock_managed' => false,
                'description' => 'Minuman redvelvet latte yang creamy',
            ],
            
            // === DESSERT ===
            [
                'name' => 'Cheesecake',
                'type' => 'dessert',
                'price' => 35000,
                'is_stock_managed' => false,
                'description' => 'Kue keju lembut dengan topping berry',
            ],
            [
                'name' => 'Brownies',
                'type' => 'dessert',
                'price' => 20000,
                'is_stock_managed' => false,
                'description' => 'Brownies coklat fudgy',
            ],
            [
                'name' => 'Puding',
                'type' => 'dessert',
                'price' => 15000,
                'is_stock_managed' => false,
                'description' => 'Puding lembut aneka rasa',
            ],
            [
                'name' => 'Es Krim',
                'type' => 'dessert',
                'price' => 18000,
                'is_stock_managed' => false,
                'description' => 'Es krim vanilla, coklat, dan strawberry',
            ],
            [
                'name' => 'Gelato',
                'type' => 'dessert',
                'price' => 25000,
                'is_stock_managed' => false,
                'description' => 'Gelato premium Italia',
            ],
            [
                'name' => 'Tiramisu',
                'type' => 'dessert',
                'price' => 38000,
                'is_stock_managed' => false,
                'description' => 'Tiramisu klasik Italia',
            ],
            [
                'name' => 'Waffle',
                'type' => 'dessert',
                'price' => 30000,
                'is_stock_managed' => false,
                'description' => 'Waffle crispy dengan topping madu',
            ],
            [
                'name' => 'Crepes',
                'type' => 'dessert',
                'price' => 28000,
                'is_stock_managed' => false,
                'description' => 'Crepes dengan berbagai topping',
            ],
            [
                'name' => 'Salad Buah',
                'type' => 'dessert',
                'price' => 22000,
                'is_stock_managed' => false,
                'description' => 'Salad buah segar dengan yogurt',
            ],
            [
                'name' => 'Mousse',
                'type' => 'dessert',
                'price' => 32000,
                'is_stock_managed' => false,
                'description' => 'Mousse coklat lembut',
            ],
            [
                'name' => 'Macarons',
                'type' => 'dessert',
                'price' => 40000,
                'is_stock_managed' => false,
                'description' => 'Macarons Perancis aneka rasa (6 pcs)',
            ],
            [
                'name' => 'Poffertjes',
                'type' => 'dessert',
                'price' => 25000,
                'is_stock_managed' => false,
                'description' => 'Mini pancake Belanda dengan butter',
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
        $this->command->info('👤 Admin     : admin@cafe.com');
        $this->command->info('👤 Kasir     : kasir@cafe.com');
        $this->command->info('🔒 Password  : password');
        $this->command->info('=======================================');
    }
}
