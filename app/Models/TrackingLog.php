<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingLog extends Model
{
    protected $table = 'TRACKING_LOG';
    protected $primaryKey = 'tracking_id';
    
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'shipment_id',
        'port_id',
        'event_type',
        'location',
        'status',
        'remarks',
        'updated_at',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class, 'shipment_id', 'shipment_id');
    }

    public function port()
    {
        return $this->belongsTo(Port::class, 'port_id', 'port_id');
    }
}
