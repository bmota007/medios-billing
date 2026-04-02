<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteFollowup extends Model
{
    protected $fillable = [
        'quote_id',
        'followup_date',
        'status',
        'notes'
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }
}
