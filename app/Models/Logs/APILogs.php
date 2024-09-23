<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APILogs extends Model
{
    use HasFactory;
    protected $table = 'api_logs';
    protected $guarded = [];
}
