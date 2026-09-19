<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'supplier_name',
        'status',
        'expected_date',
        'notes',
        'received_at',
    ];

    protected function casts(): array
    {
        return [
            'expected_date' => 'date',
            'received_at' => 'datetime',
        ];
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function totalCost(): float
    {
        return (float) $this->items->sum(fn ($item) => $item->qty * $item->unit_cost);
    }

    public function toAdminArray(): array
    {
        return [
            'id' => $this->id,
            'supplier' => $this->supplier_name,
            'status' => $this->status,
            'expectedDate' => optional($this->expected_date)->format('Y-m-d'),
            'notes' => $this->notes,
            'items' => $this->items->map(fn ($item) => [
                'productId' => $item->product_id,
                'name' => $item->product_name,
                'qty' => $item->qty,
                'unitCost' => (float) $item->unit_cost,
            ])->values(),
            'totalCost' => $this->totalCost(),
            'createdAt' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
