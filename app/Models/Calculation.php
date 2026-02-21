<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calculation extends Model
{
    protected $fillable = [
        'month', 'year',
        'rpa', 'rbt12', 'rbt12_data',
        'tax_bracket', 'aliquota_nominal', 'parcela_deduzir',
        'aliquota_efetiva', 'valor_total_das', 'special_case',
        'irpj_percent', 'irpj_value',
        'csll_percent', 'csll_value',
        'cofins_percent', 'cofins_value',
        'pis_percent', 'pis_value',
        'cpp_percent', 'cpp_value',
        'iss_percent', 'iss_value',
    ];

    protected $casts = [
        'month'             => 'integer',
        'year'              => 'integer',
        'rbt12_data'        => 'array',
        'special_case'      => 'boolean',
        'aliquota_nominal'  => 'decimal:5',
        'aliquota_efetiva'  => 'decimal:5',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderByDesc('year')->orderByDesc('month');
    }
}
