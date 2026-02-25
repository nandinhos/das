<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxBracketVersion extends Model
{
    protected $fillable = [
        'version',
        'source',
        'payload',
        'checksum',
        'applied_at',
    ];

    protected function casts(): array
    {
        return [
            'payload'    => 'array',
            'applied_at' => 'datetime',
            'version'    => 'integer',
        ];
    }

    public static function nextVersion(): int
    {
        return (static::max('version') ?? 0) + 1;
    }
}
