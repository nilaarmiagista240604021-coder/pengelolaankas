<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'transaction_date',
        'amount',
        'type',
        'description',
    ];

    
}
