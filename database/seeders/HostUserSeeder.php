<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class HostUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create System Admin
        User::updateOrCreate(
            ['email' => 'admin@vms.com'], // Prevents duplicate errors if you run it twice
            [
                'name' => 'System Admin',
                'phone' => '+254700000000',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
            ]
        );

        // 2. Create Host A
        User::updateOrCreate(
            ['email' => 'john@vms.com'],
            [
                'name' => 'John Host',
                'phone' => '+254712345678',
                'role' => 'host',
                'password' => Hash::make('password123'),
            ]
        );

        // 3. Create Host B
        User::updateOrCreate(
            ['email' => 'jane@vms.com'],
            [
                'name' => 'Jane Resident',
                'phone' => '+254787654321',
                'role' => 'host',
                'password' => Hash::make('password123'),
            ]
        );
    }
}