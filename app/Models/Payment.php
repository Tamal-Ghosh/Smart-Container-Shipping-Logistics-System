<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'PAYMENT';
    protected $primaryKey = 'payment_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'shipment_id',
        'customer_id',
        'amount',
        'payment_method',
        'payment_status',
        'payment_date',
        'transaction_ref',
        'due_date',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'due_date' => 'datetime',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class, 'shipment_id', 'shipment_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
}
