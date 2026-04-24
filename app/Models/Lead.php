<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_name',
        'contact_name',
        'email',
        'phone',
        'source',
        'notes',
        'status',
        'value',
        'follow_up_date',
        'assigned_to',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'follow_up_date' => 'date',
    ];
}
