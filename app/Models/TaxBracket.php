<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxBracket extends Model
{
    use HasFactory;

    protected $fillable = [
        'faixa',
        'min_rbt12',
        'max_rbt12',
        'aliquota_nominal',
        'deducao',
        'irpj',
        'csll',
        'cofins',
        'pis',
        'cpp',
        'iss',
        'special_case',
    ];

    /**
     * Get the formatted minimum value.
     */
    public function getFormattedMinAttribute(): string
    {
        return 'R$ ' . number_format($this->min_rbt12, 2, ',', '.');
    }

    /**
     * Get the formatted maximum value.
     */
    public function getFormattedMaxAttribute(): string
    {
        return 'R$ ' . number_format($this->max_rbt12, 2, ',', '.');
    }
}
