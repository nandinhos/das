<?php

use App\Http\Controllers\Api\TaxBracketController;
use Illuminate\Support\Facades\Route;

Route::get('/tax-brackets/check', [TaxBracketController::class, 'check']);
Route::get('/tax-brackets', [TaxBracketController::class, 'index']);
