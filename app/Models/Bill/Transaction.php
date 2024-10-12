<?php

namespace App\Models\Bill;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'bill_transaction_id',
        'user_id',
        'transaction_type',
        'budget_id',
        'raw_merchant_name',
        'merchant_name',
        'is_locked',
        'is_reconciled',
        'receipt_required',
        'review_required',
        'occurred_time',
        'updated_time',
        'complete',
        'network',
        'is_parent',
        'amount',
        'transacted_amount',
        'fees',
        'receipt_status',
        'receipt_sync_status',
        'merchant_category_code',
        'card_present',
        'card_id',
        'currency_data',
        'tags',
        'custom_fields',
        'child_transaction_ids',
        'reviews',
        'reviewers'
    ];
}
