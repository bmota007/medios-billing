<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'logo',
        'phone',
        'address',
        'domain',
        'subdomain'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Company Users
     */
    public function users()
    {
        return $this->hasMany(User::class, 'company_id');
    }

    /**
     * Company Invoices
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'company_id');
    }

}
