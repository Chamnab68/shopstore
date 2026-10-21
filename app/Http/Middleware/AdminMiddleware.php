<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ១. ពិនិត្យមើលថាតើ User បាន Login ចូលប្រព័ន្ធហើយឬនៅ
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // ២. ហៅ function isAdmin() ពី Model User មកឆែកមើលសិទ្ធិ (is_admin == 1 ឬមាន Roles)
        if (!Auth::user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized action. Admin only.'], 403);
            }
            
            // 💡 ប្រសិនបើគាត់ជា Customer ធម្មតា ឱ្យរុញទៅកាន់ទំព័រមុខ Website ផ្ទាល់តែម្តង
            return redirect()->route('home')->with('error', 'Access denied. Admin rights required.');
        }

        // បើមានសិទ្ធិគ្រប់គ្រាន់ អនុញ្ញាតឱ្យរត់ទៅកាន់ទំព័រ Admin បន្ត
        return $next($request);
    }
}