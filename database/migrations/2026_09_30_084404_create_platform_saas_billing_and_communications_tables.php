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
        Schema::create('hotel_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('plan_name', 100)->default('Professional Cloud');
            $table->string('billing_cycle', 30)->default('monthly');
            $table->decimal('fee', 12, 2)->default(9999.00);
            $table->string('currency', 3)->default('INR');
            $table->string('status', 30)->default('active')->index();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('renews_at')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 40)->unique();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->string('currency', 3)->default('INR');
            $table->string('status', 30)->default('unpaid')->index();
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_reference', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('platform_communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('hotel_id')->nullable()->constrained()->nullOnDelete();
            $table->string('target_audience', 50)->default('hotel_admin');
            $table->string('recipient_email')->nullable();
            $table->string('subject');
            $table->text('message');
            $table->string('category', 50)->default('general');
            $table->string('status', 30)->default('sent');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_communications');
        Schema::dropIfExists('hotel_invoices');
        Schema::dropIfExists('hotel_subscriptions');
    }
};
