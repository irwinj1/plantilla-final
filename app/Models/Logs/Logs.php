<?php

namespace App\Models\Logs;

use MongoDB\Laravel\Eloquent\Model;

class Logs extends Model
{
    //
    protected $connection = 'mongodb';   // fuerza usar Mongo
    protected $collection = 'logs';      // nombre de la colección

    protected $fillable = ['action', 'ip', 'data'];
}
