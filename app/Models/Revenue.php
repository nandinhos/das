<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    protected $fillable = ['month', 'year', 'amount'];

    protected $casts = [
        'month'  => 'integer',
        'year'   => 'integer',
        'amount' => 'decimal:2',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderByDesc('year')->orderByDesc('month');
    }
}
