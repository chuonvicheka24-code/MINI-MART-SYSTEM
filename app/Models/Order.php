<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'location',
        'address',
        'truck_number',
        'transport_type',
        'payment_method',
        'subtotal',
        'delivery_fee',
        'total',
        'status',
        'delivery_staff_id',
        'placed_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'total' => 'decimal:2',
            'placed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function deliveryStaff()
    {
        return $this->belongsTo(DeliveryStaff::class);
    }

    public function itemCount(): int
    {
        return $this->items->sum('qty');
    }

    /** "Truck TM-1042" / "Motorcycle" — matches the original admin.js "mode" string. */
    public function getModeLabelAttribute(): string
    {
        return $this->transport_type === 'truck'
            ? 'Truck '.($this->truck_number ?: '—')
            : 'Motorcycle';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'out' => 'Out for delivery',
            'done' => 'Delivered',
            default => ucfirst($this->status),
        };
    }
}
