<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiKeyController extends Controller
{
    public function index(Request $request)
    {
        $keys = ApiKey::where('user_id',$request->user()->id)->get();
        return response()->json(['success'=>true,'data'=>$keys]);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(),['name'=>'required|string|max:100','permissions'=>'array']);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()],422);

        $key = ApiKey::generate($request->name, $request->user()->id, $request->permissions ?? ['read']);
        return response()->json(['success'=>true,'data'=>$key,'key'=>$key->key,'message'=>'Save this key, it will not be shown again!'],201);
    }

    public function destroy(Request $request, $id)
    {
        ApiKey::where('user_id',$request->user()->id)->findOrFail($id)->delete();
        return response()->json(['success'=>true,'message'=>'API key deleted.']);
    }
}
