<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    protected $fillable = [
        'contract_id',
        'customer_name',
        'customer_email',
        'signature_image',
        'ip_address',
        'user_agent',
        'signed_at'
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
