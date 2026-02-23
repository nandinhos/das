<?php

use App\Http\Controllers\Api\TaxBracketController;
use App\Services\TaxBracketScraperService;
use Illuminate\Support\Facades\Route;

Route::get('/tax-brackets/check', [TaxBracketController::class, 'check']);
Route::get('/tax-brackets', [TaxBracketController::class, 'index']);
Route::get('/tax-brackets/official', function () {
    $scraper = new TaxBracketScraperService;

    return response()->json([
        'source' => 'site_planalto',
        'brackets' => $scraper->fetchOfficialBrackets(),
    ]);
});
