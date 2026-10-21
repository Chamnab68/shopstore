<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'bank_name'           => Setting::get('bank_name', 'ACLEDA Bank'),
            'bank_account_name'   => Setting::get('bank_account_name', 'ShopStore Co., Ltd'),
            'bank_account_number' => Setting::get('bank_account_number', '000123456'),
            'bank_qr_usd'         => Setting::get('bank_qr_usd'),
            'bank_qr_khr'         => Setting::get('bank_qr_khr'),
        ];
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'bank_name'           => 'required|string|max:100',
            'bank_account_name'   => 'required|string|max:100',
            'bank_account_number' => 'required|string|max:50',
            'bank_qr_usd'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'bank_qr_khr'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        Setting::set('bank_name',           $request->bank_name);
        Setting::set('bank_account_name',   $request->bank_account_name);
        Setting::set('bank_account_number', $request->bank_account_number);

        if ($request->hasFile('bank_qr_usd')) {
            $old = Setting::get('bank_qr_usd');
            if ($old) Storage::disk('public')->delete($old);
            Setting::set('bank_qr_usd', $request->file('bank_qr_usd')->store('settings', 'public'));
        }

        if ($request->hasFile('bank_qr_khr')) {
            $old = Setting::get('bank_qr_khr');
            if ($old) Storage::disk('public')->delete($old);
            Setting::set('bank_qr_khr', $request->file('bank_qr_khr')->store('settings', 'public'));
        }

        return redirect()->route('admin.settings.index')->with('success', '✅ Payment settings updated!');
    }

    public function deleteQr(Request $request)
    {
        $type = $request->type ?? 'usd';
        $key  = 'bank_qr_' . $type;
        $old  = Setting::get($key);
        if ($old) Storage::disk('public')->delete($old);
        Setting::set($key, null);
        return response()->json(['success' => true, 'message' => 'QR Code removed.']);
    }
}
