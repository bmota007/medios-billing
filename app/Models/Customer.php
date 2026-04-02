<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'company_name',
        'email',
        'phone',
        'billing_address',
        'street_address',
        'city',
        'state',
        'zip',
        'city_state_zip',
        'slug',
    ];

    /**
     * Relationship to the Company (Multi-tenancy)
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relationship to Invoices
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Relationship to Quotes
     */
    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }
}
