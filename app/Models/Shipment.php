<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $table = 'SHIPMENT';
    protected $primaryKey = 'shipment_id';
    
    // In Oracle, sequence + trigger handles ID auto-generation
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'customer_id',
        'source_port_id',
        'destination_port_id',
        'vehicle_id',
        'created_by',
        'shipment_ref',
        'status',
        'shipment_date',
        'expected_delivery_date',
        'actual_delivery_date',
        'notes',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'vehicle_id');
    }

    public function sourcePort()
    {
        return $this->belongsTo(Port::class, 'source_port_id', 'port_id');
    }

    public function destinationPort()
    {
        return $this->belongsTo(Port::class, 'destination_port_id', 'port_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'shipment_id', 'shipment_id');
    }
}
