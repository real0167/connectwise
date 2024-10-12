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
        Schema::create('cw_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cw_product_id')->unique();
            $table->json('catalog_item')->nullable();
            $table->string('description');
            $table->decimal('sequence_number', 10, 6);
            $table->decimal('quantity', 10, 2);
            $table->json('unit_of_measure')->nullable(); 
            $table->decimal('price', 10, 2);
            $table->decimal('cost', 10, 2);
            $table->decimal('ext_price', 10, 2);
            $table->decimal('ext_cost', 10, 2);
            $table->decimal('margin', 10, 2);
            $table->decimal('agreement_amount', 10, 2);
            $table->string('billable_option');
            $table->boolean('taxable_flag')->default(false);
            $table->boolean('dropship_flag')->default(false);
            $table->boolean('special_order_flag')->default(false);
            $table->boolean('phase_product_flag')->default(false);
            $table->boolean('cancelled_flag')->default(false);
            $table->decimal('quantity_cancelled', 10, 2)->default(0.00);
            $table->boolean('product_supplied_flag')->default(false);
            $table->boolean('calculated_price_flag')->default(false);
            $table->boolean('calculated_cost_flag')->default(false);
            $table->boolean('need_to_purchase_flag')->default(false);
            $table->boolean('minimum_stock_flag')->default(false);
            $table->boolean('po_approved_flag')->default(false);
            $table->json('location')->nullable();
            $table->json('business_unit')->nullable(); 
            $table->string('vendor_sku')->nullable();
            $table->text('customer_description')->nullable();
            $table->decimal('sub_contractor_amount_limit', 10, 2)->default(0.00);
            $table->json('opportunity')->nullable(); 
            $table->bigInteger('forecast_detail_id')->nullable();
            $table->json('forecast_status')->nullable();
            $table->string('product_class')->nullable();
            $table->json('tax_code')->nullable();
            $table->json('company')->nullable(); 
            $table->string('uom')->nullable();
            $table->timestamp('purchase_date')->nullable();
            $table->decimal('list_price', 10, 2)->nullable();
            $table->json('_info')->nullable();

        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cw_products');
    }
};
