<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Scopes\CompanyScope;

class Invoice extends Model
{

    protected $fillable = [
        'company_id',
        'customer_name',
        'customer_email',
        'street_address',
        'city_state_zip',
        'invoice_date',
        'due_date',
        'items',
        'notes',
        'invoice_no',
        'total',
        'status',
        'paid_at'
    ];

    protected $casts = [
        'items' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Multi-Tenant Protection
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        // Automatically assign company_id when creating invoices
        static::creating(function ($invoice) {
            if (Auth::check()) {
                $invoice->company_id = Auth::user()->company_id;
            }
        });

        // Global tenant scope (prevents cross-company data leaks)
        static::addGlobalScope(new CompanyScope);
    }
}
