<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PingController extends Controller
{
    public function handle(Request $request)
    {
        $host = $request->query('host');

        dd('test >> ', shell_exec('echo Hello'));


        if (!filter_var($host, FILTER_VALIDATE_DOMAIN) && !filter_var($host, FILTER_VALIDATE_IP)) {
            return response()->json(['error' => 'Invalid host'], 400);
        }

        $cacheKey = 'ping:' . $host;
        $data = Cache::remember($cacheKey, now()->addSeconds(30), function () use ($host) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $output = shell_exec("ping -n 1 " . escapeshellarg($host));
            } else {
                $output = shell_exec("ping -c 1 " . escapeshellarg($host));
                // dd(escapeshellarg($host));
            }

            if (!$output) {
                return [
                    'host' => $host,
                    'status' => 'unreachable',
                    'output' => null,
                ];
            }

            return [
                'host' => $host,
                'status' => 'reachable',
                'output' => $output,
            ];
        });
        // dd('test');
        return response()->json($data)->header('Cache-Control', 'max-age=30');
        // return response()->json($data);
        // return response()->json($data, 200, [], JSON_PRETTY_PRINT);
    }
}
