<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Port extends Model
{
    protected $table = 'PORT';
    protected $primaryKey = 'port_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'port_name',
        'port_code',
        'location',
        'country',
        'status',
    ];
}
