<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'payment_statu_id',
        'wallet_id',
        'token_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $hidden = [
        'id'
    ];

    public function wallet()
    {
        return $this->belongsTo( 'App\Models\Wallet', 'wallet_id', 'id' );
    }
}