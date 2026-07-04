<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContainerAssignment extends Model
{
    protected $table = 'CONTAINER_ASSIGNMENT';
    protected $primaryKey = 'assignment_id';
    
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'shipment_id',
        'container_id',
        'seal_number',
        'loaded_weight_kg',
        'assigned_at',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class, 'shipment_id', 'shipment_id');
    }

    public function container()
    {
        return $this->belongsTo(Container::class, 'container_id', 'container_id');
    }
}
