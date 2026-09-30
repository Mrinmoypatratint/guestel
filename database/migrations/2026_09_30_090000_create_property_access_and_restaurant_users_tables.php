<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Property Access Model for multi-property hospitality scalability
        Schema::create('property_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('property_type', 40)->default('hotel')->index(); // 'hotel', 'restaurant', 'resort'
            $table->unsignedBigInteger('property_id')->index();
            $table->foreignId('role_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20)->default('active')->index();
            $table->timestamps();

            $table->unique(['user_id', 'property_type', 'property_id'], 'user_property_access_unique');
        });

        // 2. Direct Restaurant Users mapping
        Schema::create('restaurant_users', function (Blueprint $table) {
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 40)->default('staff'); // 'chef', 'manager', 'waiter'
            $table->string('status', 20)->default('active')->index();
            $table->timestamps();

            $table->primary(['restaurant_id', 'user_id']);
        });

        // 3. Make hotel_id nullable on restaurants for standalone restaurant support
        if (Schema::hasColumn('restaurants', 'hotel_id')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->unsignedBigInteger('hotel_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_users');
        Schema::dropIfExists('property_accesses');
    }
};
