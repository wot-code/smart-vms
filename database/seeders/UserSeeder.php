<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create System Admin
        User::updateOrCreate(
            ['email' => 'admin@vms.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '+254700000001',
                'department' => 'Management',
            ]
        );

        // 2. Create a Test Guard
        // We add a phone here because the Guard might need to receive system alerts
        User::updateOrCreate(
            ['email' => 'guard@vms.com'],
            [
                'name' => 'Main Gate Guard',
                'password' => Hash::make('password123'),
                'role' => 'guard',
                'phone' => '+254700000002',
                'department' => 'Security',
            ]
        );

        // 3. Create a Test Host (Resident/Staff)
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

        // 4. Trigger other Seeders
        // This ensures your "Baby Keam" visitor data is loaded too
        $this->call([
            VisitorSeeder::class,
        ]);
    }
}