<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create System Admin
        User::updateOrCreate(
            ['email' => 'admin@vms.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // 2. Create Gate Guard
        User::updateOrCreate(
            ['email' => 'guard@vms.com'],
            [
                'name' => 'Main Gate Guard',
                'password' => Hash::make('password123'),
                'role' => 'guard',
            ]
        );

        // 3. Create Host
        User::updateOrCreate(
            ['email' => 'host@vms.com'],
            [
                'name' => 'Alice Wanjiru',
                'password' => Hash::make('password123'),
                'role' => 'host',
                'department' => 'IT Department',
                'phone' => '+254700000000'
            ]
        );

        // 4. Run other specific seeders
        $this->call([
            VisitorSeeder::class,
            // UserSeeder::class, // Uncomment this only after fixing the class name inside it!
        ]);
    }
}