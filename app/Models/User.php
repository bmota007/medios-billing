<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'role',
        'is_admin',
        'logo',
        'legal_accepted_at',
        'needs_password_change', // Added this
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
        'legal_accepted_at' => 'datetime',
        'needs_password_change' => 'boolean', // Added this
    ];

    /**
     * Relationship: User belongs to a Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
