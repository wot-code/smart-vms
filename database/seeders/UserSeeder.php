public function run(): void {
    // Create an Admin
    \App\Models\User::create([
        'name' => 'System Admin',
        'email' => 'admin@vms.com',
        'password' => bcrypt('password'),
        'role' => 'admin',
        'phone' => '+254700000000'
    ]);

    // Create a Host (Resident/Office)
    \App\Models\User::create([
        'name' => 'John Resident',
        'email' => 'john@vms.com',
        'password' => bcrypt('password'),
        'role' => 'host',
        'phone' => '+254712345678'
    ]);
}