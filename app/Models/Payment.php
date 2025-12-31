<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'amount',
        'provider',
        'status',
        'metadata',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'metadata' => 'array',
        'amount' => 'decimal:2',
    ];
}
