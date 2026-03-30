<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'amount',
        'type',
        'account_id'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
