<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TaxBracketComparatorService;
use Illuminate\Http\JsonResponse;

class TaxBracketController extends Controller
{
    public function __construct(
        private TaxBracketComparatorService $comparator
    ) {}

    public function check(): JsonResponse
    {
        $result = $this->comparator->checkForUpdates();

        return response()->json($result);
    }

    public function index(): JsonResponse
    {
        $local = $this->comparator->getLocalBrackets();
        $official = $this->comparator->getOfficialBrackets();

        return response()->json([
            'local' => $local,
            'official' => $official,
        ]);
    }
}
