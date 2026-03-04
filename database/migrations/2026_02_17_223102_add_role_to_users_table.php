<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This adds the 'role' column to your users table.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Roles: admin, host, security
            $table->string('role')->default('host')->after('email'); 
        });
    }

    /**
     * Reverse the migrations.
     * This removes the 'role' column if you roll back the migration.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};