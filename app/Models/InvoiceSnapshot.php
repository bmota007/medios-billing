<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InvoiceSnapshot extends Model
{
    protected $fillable = [

        'invoice_id',
        'invoice_no',
        'public_token',
        'snapshot_type',
        'snapshot_data',
        'amount',
        'payment_reference',
        'snapshot_created_at',

    ];

    protected $casts = [

        'snapshot_data' => 'array',
        'snapshot_created_at' => 'datetime',

    ];

    protected static function booted()
    {
        static::creating(function ($snapshot) {

            if (!$snapshot->public_token) {

                $snapshot->public_token =
                    Str::random(40);

            }

        });
    }

    public function invoice()
    {
        return $this->belongsTo(
            Invoice::class
        );
    }
}
