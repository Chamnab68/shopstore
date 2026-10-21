<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Referral;

class TrackReferral
{
    public function handle(Request $request, Closure $next)
    {
        // បើមានភ្ជាប់ ?ref=CODE មកជាមួយលីង
        if ($request->has('ref')) {
            $code = $request->query('ref');
            
            // ពិនិត្យមើលថាតើកូដនេះពិតជាមាននៅក្នុង Table referrals មែនដែរឬទេ
            $referralExists = Referral::where('referral_link_code', $code)->exists();
            
            if ($referralExists) {
                // រក្សាទុកកូដណែនាំទៅក្នុង Session រយៈពេលវែង
                session(['active_referral_code' => $code]);
            }
        }

        return $next($request);
    }
}