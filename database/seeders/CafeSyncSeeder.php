<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Table;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CafeSyncSeeder extends Seeder
{
    public function run()
    {
        // Create Admin User
        User::create([
            'name' => 'Admin CafeSync',
            'email' => 'admin@cafesync.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'admin', // Role admin
        ]);

        // Create Regular User (optional)
        User::create([
            'name' => 'User CafeSync',
            'email' => 'user@cafesync.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'user', // Role user
        ]);

        // Create Categories with slug
        $categories = [
            [
                'name' => 'Coffee', 
                'slug' => Str::slug('Coffee'),
                'description' => 'Hot and cold coffee beverages'
            ],
            [
                'name' => 'Non-Coffee', 
                'slug' => Str::slug('Non-Coffee'),
                'description' => 'Tea, juice, and other beverages'
            ],
            [
                'name' => 'Food', 
                'slug' => Str::slug('Food'),
                'description' => 'Snacks, pastries, and meals'
            ],
            [
                'name' => 'Dessert', 
                'slug' => Str::slug('Dessert'),
                'description' => 'Sweet treats and desserts'
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create Menus with slug
        $menus = [
            // Coffee
            [
                'category_id' => 1, 
                'name' => 'Espresso', 
                'slug' => Str::slug('Espresso'),
                'description' => 'Rich and bold espresso shot', 
                'price' => 25000, 
                'stock' => 50,
                'is_available' => true
            ],
            [
                'category_id' => 1, 
                'name' => 'Americano', 
                'slug' => Str::slug('Americano'),
                'description' => 'Espresso with hot water', 
                'price' => 30000, 
                'stock' => 45,
                'is_available' => true
            ],
            [
                'category_id' => 1, 
                'name' => 'Cappuccino', 
                'slug' => Str::slug('Cappuccino'),
                'description' => 'Espresso with steamed milk and foam', 
                'price' => 35000, 
                'stock' => 40,
                'is_available' => true
            ],
            [
                'category_id' => 1, 
                'name' => 'Latte', 
                'slug' => Str::slug('Latte'),
                'description' => 'Espresso with steamed milk', 
                'price' => 38000, 
                'stock' => 35,
                'is_available' => true
            ],
            [
                'category_id' => 1, 
                'name' => 'Mocha', 
                'slug' => Str::slug('Mocha'),
                'description' => 'Espresso with chocolate and steamed milk', 
                'price' => 42000, 
                'stock' => 30,
                'is_available' => true
            ],
            
            // Non-Coffee
            [
                'category_id' => 2, 
                'name' => 'Green Tea', 
                'slug' => Str::slug('Green Tea'),
                'description' => 'Fresh brewed green tea', 
                'price' => 20000, 
                'stock' => 25,
                'is_available' => true
            ],
            [
                'category_id' => 2, 
                'name' => 'Orange Juice', 
                'slug' => Str::slug('Orange Juice'),
                'description' => 'Fresh squeezed orange juice', 
                'price' => 25000, 
                'stock' => 20,
                'is_available' => true
            ],
            [
                'category_id' => 2, 
                'name' => 'Iced Tea', 
                'slug' => Str::slug('Iced Tea'),
                'description' => 'Refreshing iced tea', 
                'price' => 18000, 
                'stock' => 30,
                'is_available' => true
            ],
            
            // Food
            [
                'category_id' => 3, 
                'name' => 'Croissant', 
                'slug' => Str::slug('Croissant'),
                'description' => 'Fresh buttery croissant', 
                'price' => 20000, 
                'stock' => 15,
                'is_available' => true
            ],
            [
                'category_id' => 3, 
                'name' => 'Sandwich', 
                'slug' => Str::slug('Sandwich'),
                'description' => 'Club sandwich with fries', 
                'price' => 45000, 
                'stock' => 12,
                'is_available' => true
            ],
            [
                'category_id' => 3, 
                'name' => 'Pasta', 
                'slug' => Str::slug('Pasta'),
                'description' => 'Creamy carbonara pasta', 
                'price' => 55000, 
                'stock' => 10,
                'is_available' => true
            ],
            
            // Dessert
            [
                'category_id' => 4, 
                'name' => 'Cheesecake', 
                'slug' => Str::slug('Cheesecake'),
                'description' => 'New York style cheesecake', 
                'price' => 35000, 
                'stock' => 8,
                'is_available' => true
            ],
            [
                'category_id' => 4, 
                'name' => 'Tiramisu', 
                'slug' => Str::slug('Tiramisu'),
                'description' => 'Classic Italian tiramisu', 
                'price' => 40000, 
                'stock' => 6,
                'is_available' => true
            ],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }

        // Create Tables
        for ($i = 1; $i <= 20; $i++) {
            Table::create([
                'table_number' => 'T' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'capacity' => rand(2, 6),
                'status' => 'available', // Tambahkan status default jika diperlukan
            ]);
        }
    }
}