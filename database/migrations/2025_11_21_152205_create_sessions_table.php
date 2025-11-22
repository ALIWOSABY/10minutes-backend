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
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('full_description')->nullable();
            $table->string('image')->nullable();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trainer_id')->constrained()->cascadeOnDelete();
            $table->enum('platform_type', ['training', 'consultation'])->default('training');
            $table->decimal('price_coins', 10, 2);
            $table->integer('duration_minutes')->default(10);
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'all'])->default('all');
            $table->integer('views_count')->default(0);
            $table->integer('bookings_count')->default(0);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('reviews_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_sessions');
    }
};
