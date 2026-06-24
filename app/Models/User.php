<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'USERS';
    protected $primaryKey = 'user_id';
    public $incrementing = false; // Oracle trigger handles PK
    public $timestamps = false;

    protected $fillable = [
        'username',
        'email',
        'password_hash',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password_hash',
    ];

    // Laravel auth looks for 'password' attribute — map it to password_hash
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'user_id', 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'ADMIN';
    }

    public function isOperator(): bool
    {
        return $this->role === 'OPERATOR';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'CUSTOMER';
    }

    public function isActive(): bool
    {
        return $this->is_active === 'Y';
    }
}
