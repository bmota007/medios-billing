<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Scopes\CompanyScope;

class Quote extends Model
{
    protected $fillable = [
        'company_id',
        'customer_id',
        'quote_number',
        'public_token',
        'title',
        'description',
        'subtotal',
        'tax',
        'discount',
        'total',
        'quote_date',
        'valid_until',
        'status',
        'due_date',
        'contract_required',
        'signature_required',
        'converted_to_invoice',
        'sent_at',
        'viewed_at',
        'accepted_at',
        'declined_at',
        'internal_notes',
        'customer_notes',
        'deposit_type',
        'deposit_value',
        'deposit_amount',
        'remaining_amount',
        'remaining_due_date',
        'contract_status',
        'contract_signed_at',
        'auto_convert_after_contract',
    ];

    protected $casts = [
        'quote_date' => 'date',
        'valid_until' => 'date',
        'due_date' => 'datetime',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime',
        'remaining_due_date' => 'date',
        'contract_signed_at' => 'datetime',
        'contract_required' => 'boolean',
        'signature_required' => 'boolean',
        'converted_to_invoice' => 'boolean',
        'auto_convert_after_contract' => 'boolean',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'deposit_value' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($quote) {
            if (Auth::check() && Auth::user()->company_id && empty($quote->company_id)) {
                $quote->company_id = Auth::user()->company_id;
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function followups()
    {
        return $this->hasMany(QuoteFollowup::class);
    }
}
