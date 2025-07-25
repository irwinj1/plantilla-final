<?php

namespace App\Models\Sesiones;

use MongoDB\Laravel\Eloquent\Model;

class ActiveSesion extends Model
{
    //
    protected $connection = 'mongodb';
    protected $collection = 'active_sessions';

    protected $fillable = [
        'user_id',
        'roles',
        'permissions',
        'last_activity'
    ];

    protected $casts = [
        'roles' => 'array',
        'permissions' => 'array',
        'last_activity' => 'datetime'
    ];

}
