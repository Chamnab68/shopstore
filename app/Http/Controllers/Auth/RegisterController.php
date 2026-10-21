<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\{User, Referral, PointsTransaction, ReferralProgram};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, DB};
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        if ($request->has('ref')) {
            session(['active_referral_code' => $request->query('ref')]);
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|min:6|confirmed',
        ]);

        $referralCode = session('active_referral_code') ?? $request->input('ref');

        DB::beginTransaction();
        try {
            // ១. បង្កើតគណនី Customer ថ្មី
            $user = User::create([
                'name'          => $request->name,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'password'      => Hash::make($request->password),
                'role'          => 'customer',
                'is_active'     => true,
                'referral_code' => strtoupper(Str::random(8)),
            ]);

            $isReferred = false;
            $referrerId = null;
            $program = ReferralProgram::where('is_active', true)->first();

           // ២. ដំណើរការប្រព័ន្ធ Referral ប្រសិនបើមាន Code
            if ($referralCode) {
                $parentReferral = Referral::where('referral_link_code', $referralCode)->first();
                $referrerUser = null;
                
                if (!$parentReferral) {
                    $referrerUser = User::where('referral_code', $referralCode)->first();
                }

                $referrerId = $parentReferral ? $parentReferral->referrer_id : ($referrerUser ? $referrerUser->id : null);

                if ($referrerId && $referrerId !== $user->id) {
                    $isReferred = true;

                    // កត់ត្រាចូលតារាង referrals
                    Referral::create([
                        'referral_program_id' => $program ? $program->id : 1,
                        'referrer_id'         => $referrerId,
                        'referee_id'          => $user->id,
                        'status'              => Referral::STATUS_CONVERTED, 
                        'referral_link_code'  => strtoupper(Str::random(8)),
                        'registered_at'       => now(),
                        'converted_at'        => now(),
                        'ip_address'          => request()->ip(),
                    ]);

                    // អាប់ដេត referred_by របស់ referee
                    $user->update(['referred_by' => $referrerId]);

                    // 🎁 ផ្តល់ទឹកប្រាក់ $3 ឱ្យអ្នកណែនាំ (Referrer)
                    $referrer = User::find($referrerId);
                    if ($referrer) {
                        $referrer->increment('referral_cash_balance', 3.00); // បន្ថែមលុយ $3 ស្វ័យប្រវត្តិ

                        // កត់ត្រាប្រវត្តិលុយចូល (បើមានតារាង transaction)
                        \App\Models\ReferralCashTransaction::create([
                            'user_id'     => $referrerId,
                            'amount'      => 3.00,
                            'type'        => 'earn',
                            'description' => 'Referral bonus from ' . $user->name,
                        ]);
                    }
                }
                session()->forget('active_referral_code');
            }

            // 🎁 3. ផ្តល់ទឹកប្រាក់ $1 ឱ្យអ្នកដែលទើបចុះឈ្មោះថ្មី (Referee)
            if ($isReferred) {
                $user->increment('referral_cash_balance', 1.00); // បន្ថែមលុយ $1 ស្វ័យប្រវត្តិ

                \App\Models\ReferralCashTransaction::create([
                    'user_id'     => $user->id,
                    'amount'      => 1.00,
                    'type'        => 'earn',
                    'description' => 'Welcome referral bonus',
                ]);
            }

            DB::commit();

            Auth::login($user);
            return redirect()->route('home')->with('success', 'Welcome to ShopStore, ' . $user->name . '! Registration successful.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'មានបញ្ហាមិនប្រក្រតី៖ ' . $e->getMessage());
        }
    }
}