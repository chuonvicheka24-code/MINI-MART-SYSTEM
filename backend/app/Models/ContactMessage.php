<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'message',
        'read',
        'reply',
        'replied_at',
    ];

    protected function casts(): array
    {
        return [
            'read' => 'boolean',
            'replied_at' => 'datetime',
        ];
    }
}
