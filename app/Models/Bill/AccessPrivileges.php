<?php

namespace App\Models\Bill;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessPrivileges extends Model
{
    use HasFactory;
    protected $table = 'access_privileges';
    protected $guarded = [];
}
