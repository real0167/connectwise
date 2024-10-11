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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('bill_card_id', 191)->unique();
            $table->string('name')->nullable();
            $table->string('user_id')->nullable();
            $table->string('budget_id')->nullable();
            $table->string('last_four', 4)->nullable();
            $table->string('valid_thru')->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('status')->nullable();
            $table->string('type')->nullable();
            $table->boolean('share_budget_funds')->default(false)->nullable();
            $table->boolean('recurring')->default(false)->nullable();
            $table->decimal('recurring_limit', 10, 2)->nullable();
            $table->decimal('current_period_limit', 10, 2)->nullable();
            $table->decimal('current_period_spent', 10, 2)->nullable();
            $table->timestamp('created_time')->nullable();
            $table->timestamp('updated_time')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
