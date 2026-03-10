<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Product::create([
            'name' => 'Coffee',
            'price' => 2.50,
            'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQXG3JxYp8o1z1LZQX6n1j3Z0bX5FzV8vYyWw&s',
            'description' => 'A hot beverage made from roasted coffee beans.',
        ]);
        Product::create([
            'name' => 'Tea',
            'price' => 2.50,
            'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSuT3k6syay0g3vQVgi4QO3LnDPbSBoUO3plQ&s',
            'description' => 'A hot beverage made by infusing dried tea leaves in boiling water.',
        ]);


    }
}
