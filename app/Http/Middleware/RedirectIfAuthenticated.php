<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                
                // 💡 បង្ខំផ្លូវចំៗមកកាន់ Admin Dashboard ប្រសិនបើគាត់មានសិទ្ធិជាបុគ្គលិក ឬ Admin
                if ($user->isAdmin() || $user->is_admin == 1 || $user->roles()->exists()) {
                    return redirect()->route('admin.dashboard');
                }

                // បើជា Customer ធម្មតា ឱ្យទៅទំព័រមុខ
                return redirect()->route('home');
            }
        }

        return $next($request);
    }
}