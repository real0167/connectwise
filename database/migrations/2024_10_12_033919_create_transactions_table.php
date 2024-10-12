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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            
            $table->string('bill_transaction_id', 191)->unique();
            $table->string('user_id');
            $table->string('transaction_type');
            $table->string('budget_id');
            $table->string('raw_merchant_name')->nullable();
            $table->string('merchant_name')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_reconciled')->default(false);
            $table->boolean('receipt_required')->default(false);
            $table->boolean('review_required')->default(false);
            $table->timestamp('occurred_time')->nullable();
            $table->timestamp('updated_time')->nullable();
            $table->boolean('complete')->default(false);
            $table->string('network')->nullable();
            $table->boolean('is_parent')->default(false);
            $table->decimal('amount', 10, 2);
            $table->decimal('transacted_amount', 10, 2);
            $table->decimal('fees', 10, 2)->default(0);
            $table->string('receipt_status')->nullable();
            $table->string('receipt_sync_status')->nullable();
            $table->string('merchant_category_code')->nullable();
            $table->boolean('card_present')->default(false);
            $table->string('card_id');
            
            // JSON fields for complex objects
            $table->json('currency_data')->nullable();
            $table->json('tags')->nullable();
            $table->json('custom_fields')->nullable();
            $table->json('child_transaction_ids')->nullable();
            $table->json('reviews')->nullable();
            $table->json('reviewers')->nullable(); 
            
            // Add timestamps (created_at, updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
