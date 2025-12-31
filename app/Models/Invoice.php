<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'order_id',
        'customer_name',
        'items',
        'url',
        'status',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'items' => 'array',
    ];
}
