<?php

namespace App\Models\Cw;

use App\Models\Bill\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'cw_products';

    // Specify the fields that can be mass assigned
    protected $fillable = [
        'cw_product_id',
        'catalog_item',
        'description',
        'sequence_number',
        'quantity',
        'unit_of_measure',
        'price',
        'cost',
        'ext_price',
        'ext_cost',
        'margin',
        'agreement_amount',
        'billable_option',
        'location_id',
        'business_unit_id',
        'vendor_sku',
        'taxable_flag',
        'dropship_flag',
        'special_order_flag',
        'phase_product_flag',
        'cancelled_flag',
        'quantity_cancelled',
        'customer_description',
        'product_supplied_flag',
        'sub_contractor_amount_limit',
        'opportunity',
        'calculated_price_flag',
        'calculated_cost_flag',
        'forecast_detail_id',
        'purchase_date',
        'tax_code',
        'list_price',
        'company_id',
        'company_name',
        'company_identifier',
        'company',
        'forecast_status',
        'product_class',
        'need_to_purchase_flag',
        'minimum_stock_flag',
        'po_approved_flag',
        'uom',
        '_info'
    ];

    // Cast specific fields as JSON
    protected $casts = [
        'catalog_item' => 'array',
        'unit_of_measure' => 'array',
        'location' => 'array',
        'business_unit' => 'array',
        'opportunity' => 'array',
        'tax_code' => 'array',
        'company' => 'array',
        'forecast_status' => 'array',
        '_info' => 'array',
    ];
}
