<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'price',
        'unit',
        'image',
        'emoji',
        'qty',
        'discount_percent',
        'date_added',
        'expiry_date',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'date_added' => 'date',
            'expiry_date' => 'date',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_product');
    }

    /** Price after any active admin discount is applied. */
    public function getSalePriceAttribute(): float
    {
        if (! $this->discount_percent) {
            return (float) $this->price;
        }

        return round((float) $this->price * (1 - $this->discount_percent / 100), 2);
    }

    public function isLowStock(int $threshold = 10): bool
    {
        return $this->qty < $threshold;
    }

    /**
     * Turn whatever is stored in an `image` column into something an <img src>
     * can use directly, whichever of these it happens to be:
     *   - a base64 data URI  (data:image/...)   — from the admin photo-upload form
     *   - a full external URL (http:// / https://)
     *   - a plain path under public/ (Photo/Fruit/banana.jpg) — the seeded catalog
     *   - empty/null — falls back to a placeholder graphic
     */
    public static function resolveImageUrl(?string $path): string
    {
        if (! $path) {
            return asset('images/placeholder.jpg');
        }

        if (str_starts_with($path, 'data:')
            || str_starts_with($path, 'http://')
            || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    }

    /** Absolute, ready-to-use <img src> for this product. */
    public function getImageUrlAttribute(): string
    {
        return static::resolveImageUrl($this->image);
    }

    public function isExpired(): bool
    {
        return $this->expiry_date !== null && $this->expiry_date->copy()->endOfDay()->isPast();
    }

    /** Within the next N days (but not already expired). */
    public function isExpiringSoon(int $days = 30): bool
    {
        if (! $this->expiry_date || $this->isExpired()) {
            return false;
        }

        return $this->expiry_date->lessThanOrEqualTo(now()->addDays($days));
    }

    /**
     * Shape used everywhere the original storefront JS expected a "PRODUCTS"
     * row: { id, name, cat, price, unit, emoji, qty, img, dateAdded }.
     */
    public function toStorefrontArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'cat' => $this->category?->name,
            'price' => (float) $this->price,
            'salePrice' => $this->sale_price,
            'discountPercent' => $this->discount_percent,
            'unit' => $this->unit,
            'emoji' => $this->emoji,
            'qty' => $this->qty,
            'img' => $this->image_url,
            'dateAdded' => optional($this->date_added)->format('Y-m-d'),
            'expiryDate' => optional($this->expiry_date)->format('Y-m-d'),
        ];
    }
}
