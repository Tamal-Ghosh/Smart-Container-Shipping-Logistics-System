<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    protected $table = 'CONTAINER';
    protected $primaryKey = 'container_id';
    
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'container_number',
        'container_type',
        'status',
    ];

    public function assignments()
    {
        return $this->hasMany(ContainerAssignment::class, 'container_id', 'container_id');
    }
}
