<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceEvent extends Model
{
    protected $fillable = [

        'invoice_id',

        'company_id',

        'user_id',

        'event_type',

        'title',

        'description',

        'event_data',

        'ip_address',

    ];

    protected $casts = [

        'event_data' => 'array',

    ];

    public function invoice()
    {
        return $this->belongsTo(
            \App\Models\Invoice::class
        );
    }

    public function user()
    {
        return $this->belongsTo(
            \App\Models\User::class
        );
    }
}
