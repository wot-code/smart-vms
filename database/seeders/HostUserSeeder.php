<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create System Admin
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@vms.com',
            'phone' => '+254700000000',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
        ]);

        // 2. Create Host A (Resident/Staff)
        User::create([
            'name' => 'John Host',
            'email' => 'john@vms.com',
            'phone' => '+254712345678',
            'role' => 'host',
            'password' => Hash::make('password123'),
        ]);

        // 3. Create Host B (Resident/Staff)
        User::create([
            'name' => 'Jane Resident',
            'email' => 'jane@vms.com',
            'phone' => '+254787654321',
            'role' => 'host',
            'password' => Hash::make('password123'),
        ]);
    }
}