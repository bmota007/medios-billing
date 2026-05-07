<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'company_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'street_address',
        'city_state_zip',
        'invoice_no',
        'invoice_date',
        'due_date',
        'subtotal',
        'discount',
        'tax',
        'total',
        'deposit_amount',
        'notes',
        'status',
        'paid_at',
        'payment_method',
        'payment_reference',
        'check_number',
        'payment_notes',
        'items',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'total' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function items_relation()
    {
        return $this->hasMany(\App\Models\InvoiceItem::class, 'invoice_id', 'id');
    }

    /**
     * This helper ensures that if we have JSON items in the column, 
     * they are decoded properly for the view loops.
     */
    public function getDecodedItemsAttribute()
    {
        return json_decode($this->items, true) ?? [];
    }
}
