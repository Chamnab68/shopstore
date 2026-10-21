<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // ត្រួតពិនិត្យស្ថានភាពគណនី
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been disabled.']);
            }

            // 💡 វិធីឆែកថ្មីដែលរឹងមាំបំផុត៖
            // ១. បើគាត់មាន role ស្មើនឹង 'admin' (លក្ខខណ្ឌចាស់ក្នុង ShopStore)
            // ២. បើគាត់មានភ្ជាប់ role_id (មិនមែនជា customer ឬមិនទទេ)
            // ៣. បើគាត់មានឈ្មោះជាប់ក្នុងតារាង pivot 'role_user'
            $isStaffOrAdmin = ($user->role === 'admin') || 
                              ($user->role_id !== null && $user->role_id != 1) || 
                              $user->roles()->exists();

            if ($isStaffOrAdmin) {
                // បង្ខំឱ្យរត់ចូល Admin Dashboard ភ្លាមៗ
                return redirect()->route('admin.dashboard');
            }
            
            // បើជា Customer ធម្មតា ឱ្យទៅទំព័រមុខលក់ទំនិញ
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}