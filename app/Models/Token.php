<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Token extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'token_statu_id',
        'name',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $hidden = [
        'id'
    ];

    public function payment()
    {
        return $this->belongsTo( 'App\Models\Payment', 'id', 'token_id' );
    }
}
