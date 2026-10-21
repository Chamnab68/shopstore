<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Validator};

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone'    => 'nullable|string|max:20',
        ]);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()], 422);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'role'     => 'customer',
            'is_active'=> true,
        ]);
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json(['success'=>true,'token'=>$token,'user'=>$user], 201);
    }

    public function login(Request $request)
    {
        $v = Validator::make($request->all(), ['email'=>'required|email','password'=>'required']);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()], 422);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['success'=>false,'message'=>'Invalid credentials.'], 401);
        }
        if (!$user->is_active) {
            return response()->json(['success'=>false,'message'=>'Account disabled.'], 403);
        }
        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json(['success'=>true,'token'=>$token,'user'=>$user]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success'=>true,'message'=>'Logged out.']);
    }

    public function me(Request $request)
    {
        return response()->json(['success'=>true,'user'=>$request->user()]);
    }
}
