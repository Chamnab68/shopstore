<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Module #1 — Tier-Gated Feature Access Control.
 * Usage: Route::middleware('permission:rewards.create')->group(...)
 * Route::middleware('permission:tiers.override,platinum')->group(...) // tier-gated
 */
class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission, ?string $minTier = null): Response
    {
        $user = auth()->user();

        // ១. បើមិនទាន់ Login ទេ មិនអនុញ្ញាតឱ្យចូលឡើយ
        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        // ២. 💡 បើគាត់ជា Super Admin ឬជាក្រុម Admin/Staff (មាន Roles ក្នុងប្រព័ន្ធ) គឺបើកសិទ្ធិឱ្យឆ្លងកាត់ទាំងអស់តែម្តង
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return $next($request);
        }

        // ៣. ឆែកមើលប្រព័ន្ធ Permission ធម្មតាសម្រាប់ User ផ្សេងៗ
        if (!$user->hasPermission($permission)) {
            abort(403, "You do not have permission to perform this action: {$permission}");
        }

        // Tier-gated feature: e.g. only Platinum customers can access exclusive rewards
        if ($minTier && (!$user->tier || strtolower($user->tier->name) !== strtolower($minTier))) {
            $requiredTier = ucfirst($minTier);
            abort(403, "This feature is exclusive to {$requiredTier} tier members.");
        }

        return $next($request);
    }
}