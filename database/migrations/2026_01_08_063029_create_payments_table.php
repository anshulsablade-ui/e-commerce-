<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained();
            $table->enum('gateway', ['paypal', 'stripe']);
            $table->string('transaction_id', 150);
            // PayPal: capture_id / order_id
            // Stripe: payment_intent_id / charge_id

            $table->decimal('amount', 10, 2);
            $table->char('currency', 3)->default('USD');

            $table->enum('status', ['created', 'pending', 'success', 'failed', 'refunded'])->default('created');
            $table->json('response')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
