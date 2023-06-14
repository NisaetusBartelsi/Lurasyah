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
            'name' => 'Lurasyah',
            'role' => 'Super Admin',
            'email' => 'Lurasyah@gmail.com',
            'password' => bcrypt('Lurasyah'),
            'provinsi' => 'Lurasyah',
            'kota' => 'Lurasyah',
            'kecamatan' => 'Lurasyah',
            'desa' => 'Lurasyah',
        ]);
    }
}
