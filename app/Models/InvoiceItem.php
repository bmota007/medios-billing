<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $table = 'invoice_items';

    public $timestamps = true;

    protected $fillable = [
        'invoice_id',
        'service_name',
        'quantity',
        'unit_price',
        'line_total',
    ];

    protected $casts = [
        'invoice_id' => 'integer',
        'quantity' => 'float',
        'unit_price' => 'float',
        'line_total' => 'float',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP (CRITICAL)
    |--------------------------------------------------------------------------
    */
    public function invoice()
    {
        return $this->belongsTo(\App\Models\Invoice::class, 'invoice_id', 'id');
    }
}
