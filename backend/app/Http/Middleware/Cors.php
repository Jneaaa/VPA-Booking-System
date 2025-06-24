<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->getMethod() === 'OPTIONS') {
            // Handle preflight request
            return response('', 204)
                ->header('Access-Control-Allow-Origin', '*') // Allow all origins
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS') // Allowed methods
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With') // Allowed headers
                ->header('Access-Control-Allow-Credentials', 'true'); // Allow credentials
        }

        $response = $next($request);

        // Add CORS headers to the response
        return $response
            ->header('Access-Control-Allow-Origin', '*') // Allow all origins
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS') // Allowed methods
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With') // Allowed headers
            ->header('Access-Control-Allow-Credentials', 'true'); // Allow credentials
    }
}
