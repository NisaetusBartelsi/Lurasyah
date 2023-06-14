<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Sang Pemilik',
            'role' => 'Super Admin',
            'email' => 'Sang_pemilik@gmail.com',
            'password' => bcrypt('1234567890'),
        ]);
    }
}
