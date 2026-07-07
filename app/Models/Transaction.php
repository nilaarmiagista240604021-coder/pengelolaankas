<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

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

     public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
