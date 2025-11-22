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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->integer('age')->nullable()->after('phone');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('age');
            $table->foreignId('country_id')->nullable()->after('gender')->constrained()->nullOnDelete();
            $table->string('preferred_language', 10)->default('en')->after('country_id');
            $table->decimal('credit_balance', 10, 2)->default(0)->after('preferred_language');
            $table->enum('role', ['customer', 'admin'])->default('customer')->after('credit_balance');
            $table->string('avatar')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('avatar');
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            $table->string('google_id')->nullable()->after('phone_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn([
                'phone',
                'age',
                'gender',
                'country_id',
                'preferred_language',
                'credit_balance',
                'role',
                'avatar',
                'is_active',
                'phone_verified_at',
                'google_id'
            ]);
        });
    }
};
