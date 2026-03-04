<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Add an Admin
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@vms.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '0700111222',
        ]);

        // Add a Host/Resident (This will show in your dropdown)
        User::create([
            'name' => 'John Resident',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'role' => 'host',
            'phone' => '0722333444',
        ]);
        
        // Add one more for testing
        User::create([
            'name' => 'Jane Office',
            'email' => 'jane@example.com',
            'password' => Hash::make('password123'),
            'role' => 'host',
            'phone' => '0755666777',
        ]);
    }
}