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
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug')->unique();
            $table->text('bio')->nullable();
            $table->text('full_bio')->nullable();
            $table->string('avatar')->nullable();
            $table->string('specialization', 100)->nullable();
            $table->json('skills')->nullable(); // Array of skills
            $table->json('languages')->nullable(); // Array of languages
            $table->json('education')->nullable(); // Array of education entries
            $table->json('experience')->nullable(); // Array of experience entries
            $table->json('certifications')->nullable(); // Array of certifications
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_sessions')->default(0);
            $table->integer('total_reviews')->default(0);
            $table->enum('platform_type', ['training', 'consultation', 'both'])->default('both');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
};
