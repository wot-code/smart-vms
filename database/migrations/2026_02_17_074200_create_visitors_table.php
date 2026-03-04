<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone')->nullable(); // Made nullable for flexibility
            
            // 1. Added 'Delivery' to match your controller's validation
            $table->enum('type', ['Adult', 'Minor', 'Contractor', 'Delivery']); 
            
            $table->string('id_number')->nullable();      // For Adults/Contractors
            $table->string('guardian_name')->nullable();  // For Minors
            $table->string('host_name');                  // Person being visited
            $table->text('purpose');

            // 2. Added 'status' column for your "Approved/Pending" logic
            $table->string('status')->default('Approved'); 

            // 3. Time Tracking
            $table->timestamp('check_in')->useCurrent();
            $table->timestamp('check_out')->nullable();
            
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.php artisan migrate:fresh
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};