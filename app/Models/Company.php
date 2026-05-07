<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'website',
        'primary_color',

        'accept_card',
        'accept_check',
        'accept_cash',
        'accept_zelle',
        'accept_venmo',
'zelle_label',
'zelle_value',
'venmo_label',
'venmo_value',

        'logo',
        'logo_path',

        // 🔥 ADD THESE (CRITICAL FIX)
        'contract_1_name',
        'contract_1_path',
        'contract_2_name',
        'contract_2_path',
        'contract_3_name',
        'contract_3_path',
        'contract_4_name',
        'contract_4_path',

        // SMTP
        'smtp_host',
        'smtp_port',
        'smtp_user',
        'smtp_pass',
        'smtp_from',

        // Stripe
        'stripe_mode',
        'stripe_publishable_key',
        'stripe_secret_key',
        'stripe_test_publishable_key',
        'stripe_test_secret_key',
        'stripe_webhook_secret'
    ];
}
