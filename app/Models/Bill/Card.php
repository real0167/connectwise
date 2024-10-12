<?php

namespace App\Models\Bill;

use App\Models\Cw\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $table = 'cards';

    // Specify the fillable fields
    protected $fillable = [
        'bill_card_id',
        'name',
        'user_id',
        'budget_id',
        'last_four',
        'valid_thru',
        'expiration_date',
        'status',
        'type',
        'share_budget_funds',
        'recurring',
        'recurring_limit',
        'current_period_limit',
        'current_period_spent',
        'created_time',
        'updated_time',
    ];

    // Define a relationship to CwProduct (if needed)
    public function products()
    {
        return $this->belongsTo(Product::class, 'name', 'company_identifier');
    }
}
