<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'store_name',
        'hours',
        'delivery_rate',
        'low_stock_threshold',
    ];

    protected function casts(): array
    {
        return [
            'delivery_rate' => 'float',
            'low_stock_threshold' => 'integer',
        ];
    }

    /** There is only ever one settings row — fetch (or lazily create) it. */
    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'store_name' => config('app.name'),
            'hours' => '7:00 AM – 10:00 PM',
            'delivery_rate' => 10,
            'low_stock_threshold' => 10,
        ]);
    }
}
