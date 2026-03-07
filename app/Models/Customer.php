<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\CompanyScope;

class Customer extends Model
{
    protected $fillable = [
        'company_id',
        'customer_type',
        'name',
        'email',
        'phone',
        'company_name',
        'billing_address',
        'city',
        'state',
        'zip',
        'notes',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }
}
