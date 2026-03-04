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
    Schema::table('visitors', function (Blueprint $table) {
        // Adding the missing column
        $table->string('vehicle_reg')->nullable()->after('purpose'); 
    });
}

public function down(): void
{
    Schema::table('visitors', function (Blueprint $table) {
        $table->dropColumn('vehicle_reg');
    });
}
};
