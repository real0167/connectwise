<?php

namespace App\Models\Cw;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionModel extends Model
{
    use HasFactory;
    protected $table = 'transactions';
    protected $guarded = [];
}
