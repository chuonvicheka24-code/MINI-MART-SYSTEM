<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'title',
        'description',
        'discount_percent',
        'starts_at',
        'ends_at',
        'ended_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at' => 'date',
            'ended_at' => 'datetime',
        ];
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'promotion_product');
    }

    /** Ended manually, or past its end date. */
    public function isEnded(): bool
    {
        return $this->ended_at !== null || ($this->ends_at !== null && $this->ends_at->copy()->endOfDay()->isPast());
    }

    /** Not yet started (has a future start date). */
    public function isScheduled(): bool
    {
        return ! $this->isEnded() && $this->starts_at !== null && $this->starts_at->isFuture();
    }

    public function status(): string
    {
        if ($this->isEnded()) {
            return 'ended';
        }

        if ($this->isScheduled()) {
            return 'scheduled';
        }

        return 'active';
    }

    public function toAdminArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'discountPercent' => $this->discount_percent,
            'startsAt' => optional($this->starts_at)->format('Y-m-d'),
            'endsAt' => optional($this->ends_at)->format('Y-m-d'),
            'status' => $this->status(),
            'productIds' => $this->products->pluck('id')->values(),
            'productNames' => $this->products->pluck('name')->values(),
        ];
    }
}
