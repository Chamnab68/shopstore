<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // ១. ប្រសិនបើជា Super Admin ធំ (is_admin == 1) ឬមាន Role ជា super_admin គឺអនុញ្ញាតឱ្យចូលបានទាំងអស់
        if ($user->is_admin == 1 || $user->roles()->where('name', 'super_admin')->exists()) {
            return $next($request);
        }

        // ២. ពិនិត្យមើលថាតើ User នេះមាន Role ណាមួយដែលបានកំណត់ក្នុង Route ដែរឬទេ
        foreach ($roles as $role) {
            if ($user->roles()->where('name', $role)->exists()) {
                return $next($request);
            }
        }

        // បើគ្មានសិទ្ធិទេ បោះផ្ទាំង 403 Forbidden ភ្លាម
        abort(403, 'Unauthorized action.');
    }
}