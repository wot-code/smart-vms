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
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            
            // Nullable allows logging events from guests or failed logins
            $table->foreignId('user_id')
                  ->nullable() 
                  ->constrained()
                  ->onDelete('cascade');

            // Stores the type of event (e.g., UNAUTHORIZED_ACCESS)
            $table->string('action')->index(); 

            $table->string('url');
            $table->string('ip_address', 45); // 45 chars supports IPv6
            
            // text is safer than string for long browser user-agent strings
            $table->text('user_agent')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};