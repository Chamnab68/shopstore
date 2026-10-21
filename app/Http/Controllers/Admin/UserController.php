<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    // បង្ហាញបញ្ជី User ទាំងអស់ និងមានកន្លែងបង្កើតថ្មី
    public function index(Request $request)
    {
        $query = User::with('roleModel', 'tier');
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $users = $query->latest()->paginate(15);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    // រក្សាទុក Account ថ្មីដែល Admin បានបង្កើត
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id'  => 'nullable|exists:roles,id',
        ]);

        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role_id'       => $request->role_id,
            'role'          => $request->role_id ? 'admin' : 'customer',
            'referral_code' => 'M' . strtoupper(Str::random(6)),
        ]);

        return redirect()->back()->with('success', 'User account created successfully!');
    }

    // អាប់ដេត Role ឬសិទ្ធិរបស់ User
    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'role_id'  => 'nullable|exists:roles,id',
            'is_admin' => 'boolean',
        ]);

        $user->update([
            'role_id'  => $request->role_id,
            'is_admin' => $request->has('is_admin') ? 1 : 0,
            'role'     => $request->has('is_admin') ? 'admin' : 'customer',
        ]);

        return redirect()->back()->with('success', "User permissions updated successfully!");
    }

    // លុប User
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true]);
    }
}