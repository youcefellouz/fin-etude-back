<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('email', 'youssef.ellouze133@gmail.com')->exists()) {
            User::create([
                'name'     => 'youcef',
                'email'    => 'youssef.ellouze133@gmail.com',
                'password' => Hash::make('A123456789#'),
                'role'     => 'admin',
            ]);
        }
    }
}