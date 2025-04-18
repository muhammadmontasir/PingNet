<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TracerouteController extends Controller
{
    public function handle(Request $request)
    {
        $host = $request->query('host');

        if (!filter_var($host, FILTER_VALIDATE_DOMAIN) && !filter_var($host, FILTER_VALIDATE_IP)) {
            return response()->json([
                'error' => 'Invalid host.',
            ], 400);
        }

        $cacheKey = 'traceroute:' . $host;

        $output = Cache::remember($cacheKey, now()->addSecond(), function () use ($host) {
            $command = "traceroute " . escapeshellarg($host);

            exec($command, $lines, $status);
            
            return [
                'success' => $status === 0,
                'lines' => $lines,
                'raw' => implode("\n", $lines)
            ];
        });

        return response()->json([
            'host' => $host,
            'success' => $output['success'],
            'output' => $output['raw']
        ]);
    }
}
