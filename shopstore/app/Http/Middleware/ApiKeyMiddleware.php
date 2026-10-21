<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('X-API-KEY') ?? $request->query('api_key');

        if (!$key) {
            return response()->json(['success' => false, 'message' => 'API key is required. Pass X-API-KEY header.'], 401);
        }

        $apiKey = ApiKey::where('key', $key)->first();

        if (!$apiKey || !$apiKey->isValid()) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired API key.'], 401);
        }

        // Update usage
        $apiKey->update([
            'last_used_at'  => now(),
            'request_count' => $apiKey->request_count + 1,
        ]);

        // Attach to request for permission checking
        $request->merge(['_api_key' => $apiKey]);

        return $next($request);
    }
}

// ─── Admin Middleware ──────────────────────────────────────
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['success'=>false,'message'=>'Unauthorized.'],403);
            }
            abort(403, 'Access denied.');
        }
        return $next($request);
    }
}
