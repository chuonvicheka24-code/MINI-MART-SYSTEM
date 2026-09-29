<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'icon', 'image'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /** Absolute, ready-to-use <img src> — handles base64 uploads, full URLs, and plain public/ paths alike. */
    public function getImageUrlAttribute(): string
    {
        return Product::resolveImageUrl($this->image);
    }
}
