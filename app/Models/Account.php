<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'balance',
        'status',
        'account_id',
    ];

    public function account_type()
    {
        return $this->belongsTo(AccountType::class);
    }

    public function account_cards()
    {
        return $this->hasMany(AccountCard::class);
    }
}
