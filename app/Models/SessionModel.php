<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SessionModel extends Model
{
    use HasFactory, SoftDeletes;

    public $table = 'sessions';

    protected $fillable = [
        'id',
        'payload',
        'user_id',
        'last_activity',
        'created_at',
        'deleted_at',
        'updated_at'
    ];

    protected $hidden = [
        'id'
    ];
}
