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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 3)->unique(); // ISO code (e.g., 'SA', 'US')
            $table->string('currency_code', 3); // e.g., 'SAR', 'USD'
            $table->string('currency_symbol', 10); // e.g., 'SR', '$'
            $table->decimal('exchange_rate', 10, 4)->default(1.0000);
            $table->string('flag_icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
