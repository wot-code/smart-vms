<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create/Update Admin
        User::updateOrCreate(
            ['email' => 'admin@vms.com'],
            [
                'name'     => 'System Admin',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
                'phone'    => '+254700000001', // Added phone for compatibility
            ]
        );

        // 2. Create/Update Guard
        User::updateOrCreate(
            ['email' => 'guard@vms.com'],
            [
                'name'     => 'Main Gate Guard',
                'password' => Hash::make('password123'),
                'role'     => 'guard',
                'phone'    => '+254700000002',
            ]
        );

        // 3. Create/Update Host (Staff)
        User::updateOrCreate(
            ['email' => 'host@vms.com'],
            [
                'name'     => 'John Host',
                'password' => Hash::make('password123'),
                'role'     => 'host',
                'phone'    => '+254700000003',
            ]
        );
    }
}