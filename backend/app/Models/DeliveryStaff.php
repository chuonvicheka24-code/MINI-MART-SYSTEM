<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryStaff extends Model
{
    protected $table = 'delivery_staff';

    protected $fillable = ['name', 'phone', 'vehicle', 'status'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
