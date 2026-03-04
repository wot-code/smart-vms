<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // This line calls your specific VisitorSeeder
        $this->call([
            VisitorSeeder::class,
        VisitorSeeder::class,
    ]);
}
    

        // If you had a UserSeeder for login, you would add it here too:
        // UserSeeder::class,
    }
