<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DnsLookupController extends Controller
{
    public function handle(Request $request)
    {
        $host = $request->query('host');

        if (!filter_var($host, FILTER_VALIDATE_DOMAIN)) {
            return response()->json([
                'error' => 'Invalid host.',
            ], 400);
        }

        $cacheKey = 'dns:' . $host;

        $records = Cache::remember($cacheKey, now()->addHour(), function () use ($host) {
            $dns = dns_get_record($host);

            return $dns ?: [];
        });

        return response()->json([
            'host' => $host,
            'records' => $records,
        ])
        ->header('Cache-Control', 'max-age=3600, public');
    }
}
