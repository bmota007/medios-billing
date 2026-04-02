<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionInvoice extends Model
{
    protected $table = 'subscription_invoices';

    protected $fillable = [
        'company_id',
        'invoice_no',
        'stripe_invoice_id',
        'stripe_customer_id',
        'customer_name',
        'customer_email',
        'amount',
        'currency',
        'status',
        'items',
        'invoice_date',
        'paid_at',
        'period_start',
        'period_end',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'items' => 'array',
        'invoice_date' => 'datetime',
        'paid_at' => 'datetime',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
