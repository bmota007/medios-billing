<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformBranding extends Model
{
    protected $table = 'platform_brandings';

    protected $fillable = [
        'platform_name',
        'logo',
        'stripe_live_pub_key',
        'stripe_live_secret_key',
        'stripe_test_pub_key',
        'stripe_test_secret_key',
        'stripe_webhook_secret'
    ];
}

