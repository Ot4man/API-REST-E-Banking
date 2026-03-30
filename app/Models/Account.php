<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'type',
        'balance',
        'status',
        'overdraft_limit',
        'interest_rate',
        'guardian_id',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('accepted_closure');
    }

    public function guardian()
    {
        return $this->belongsTo(User::class, 'guardian_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
