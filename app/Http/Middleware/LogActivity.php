<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Module #2 — Auto-log every state-changing request (POST/PUT/PATCH/DELETE).
 * Attach globally to web/api middleware group, or per-route as needed.
 */
class LogActivity
{
    public function __construct(protected ActivityLogService $activityLog) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']) && auth()->check()) {
            $routeName = $request->route()?->getName() ?? $request->path();

            $this->activityLog->log("request.{$request->method()}", null, [
                'metadata' => [
                    'route'  => $routeName,
                    'status' => $response->getStatusCode(),
                ],
            ]);
        }

        return $response;
    }
}
