<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Visitor;

class VisitorSeeder extends Seeder
{
    public function run(): void
    {
        Visitor::create([
            'full_name'     => 'Baby Keam',
            'phone'         => '0712345678',
            'type'          => 'Adult',           // Must be one of: Adult, Minor, Contractor, Delivery
            'id_number'     => '12345678',        // Added because it's in your Model/Migration
            'host_name'     => 'Admin',
            'purpose'       => 'Business Meeting',
            'status'        => 'Pending',         // Your default is 'Approved', but we set 'Pending' to test the badge
            'check_in'      => now(),
        ]);
    }
}