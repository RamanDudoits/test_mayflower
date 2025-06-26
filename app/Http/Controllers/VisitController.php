<?php

namespace App\Http\Controllers;

use App\Enum\AppKeys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class VisitController extends Controller
{
    private const REDIS_HASH_KEY = '';

    /**
     * POST /api/v1/visit
     * @param Request $request
     * @return JsonResponse
     */
    public function increment(Request $request): JsonResponse
    {
        $country = Str::lower($request->input('country'));

        if (!preg_match('/^[a-z]{2}$/', $country)) {
            return response()->json(['error' => 'Invalid country code'], 422);
        }

        try {
            Redis::hincrby(AppKeys::REDIS_HASH_KEY->value, $country, 1);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Storage error'], 500);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * GET /api/v1/stats
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = Redis::hgetall(AppKeys::REDIS_HASH_KEY->value);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        $stats = collect($stats)->map(fn($v) => (int)$v)->all();

        return response()->json($stats, 200);
    }
}
