<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Jozef Mrkva',
            'password' => bcrypt('password123'),
            'email' => 'jozefmrkva@stuba.sk'
        ]);

        User::create([
            'name' => 'Martin Budinský',
            'password' => bcrypt('12345678'),
            'email' => 'martinbudinsky@stuba.sk'
        ]);

        User::create([
            'name' => 'Jakub Muller',
            'password' => bcrypt('12345678'),
            'email' => 'jakubmuller@stuba.sk'
        ]);
    }
}
