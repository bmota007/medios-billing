<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        // BASIC INFO
        'name',
        'email',
        'logo_path',
        'logo',
        'primary_color',
        'phone',
        'address',
        'website',
        'domain',
        'subdomain',
        'street_address',
        'city_state_zip',

        // SUBSCRIPTION & STATUS
        'plan_name',
        'monthly_price',
        'subscription_status',
        'subscription_started_at',
        'subscription_ends_at',
        'is_active',
        'trial_ends_at',
        'stripe_id',
        'is_vetted',
        'status',
        'last_login_at',
        'plan',
        'mrr',
        'industry',

        // STRIPE
        'stripe_mode',
        'stripe_publishable_key',
        'stripe_secret_key',
        'stripe_test_publishable_key',
        'stripe_test_secret_key',
        'stripe_webhook_secret',
        'stripe_test_public_key',
        'stripe_test_webhook_secret',

        // LEGACY STRIPE
        'client_stripe_key',
        'client_stripe_secret',
        'client_stripe_webhook_secret',

        // LEGACY CONTRACT
        'contract_template_path',
        'contract_template_type',
        'contract_terms',

        // NEW MULTI-CONTRACT LIBRARY
        'contract_1_name',
        'contract_1_path',
        'contract_2_name',
        'contract_2_path',
        'contract_3_name',
        'contract_3_path',
        'contract_4_name',
        'contract_4_path',

        // PAYMENT METHODS
        'accept_card',
        'accept_check',
        'accept_cash',
        'accept_zelle',
        'accept_venmo',
        'zelle_label',
        'zelle_value',
        'venmo_label',
        'venmo_value',
    ];

    protected $casts = [
        'subscription_started_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'monthly_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_vetted' => 'boolean',
        'accept_card' => 'boolean',
        'accept_check' => 'boolean',
        'accept_cash' => 'boolean',
        'accept_zelle' => 'boolean',
        'accept_venmo' => 'boolean',
    ];

    public function users() { return $this->hasMany(User::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function customers() { return $this->hasMany(Customer::class); }
    public function quotes() { return $this->hasMany(Quote::class); }
}
