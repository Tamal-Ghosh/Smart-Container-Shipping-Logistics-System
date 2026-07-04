<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = 'VEHICLE';
    protected $primaryKey = 'vehicle_id';
    
    // In Oracle, sequence + trigger handles ID auto-generation, 
    // so we set incrementing to false in Eloquent.
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'vehicle_number',
        'type',
        'capacity_kg',
        'status',
    ];
}
