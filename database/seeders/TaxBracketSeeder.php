<?php

namespace Database\Seeders;

use App\Models\TaxBracketVersion;
use App\Services\TaxBracketComparatorService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxBracketSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(
            file_get_contents(database_path('seeders/data/tax_brackets_v1.json')),
            true
        );

        DB::table('tax_brackets')->insert($data);

        TaxBracketVersion::create([
            'version'    => 1,
            'source'     => 'seed',
            'payload'    => $data,
            'checksum'   => TaxBracketComparatorService::computeChecksum($data),
            'applied_at' => now(),
        ]);
    }
}
