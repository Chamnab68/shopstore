<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReferralController extends Controller
{
    public function __construct(protected ReferralService $referralService) {}

    /** 
     * My referral dashboard: link, stats, badges earned 
     */
    /** 
     * My referral dashboard: link, stats, badges earned 
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Retrieve existing referral record or generate a new link automatically
        $myReferral = $user->referralsMade()->latest()->first()
            ?? $this->referralService->generateLink($user);

        // Calculate statistics
        $totalReferred = $user->referralsMade()->count();
        $convertedCount = $user->referralsMade()->where('status', Referral::STATUS_CONVERTED)->count();
        $pendingCount = $user->referralsMade()->where('status', Referral::STATUS_REGISTERED)->count();

        // Method 1: Calculate total earnings from the joined referral_commissions table
        $totalEarned = $user->referralsMade()
            ->join('referral_commissions', 'referrals.id', '=', 'referral_commissions.referral_id')
            ->sum('referral_commissions.points_awarded');

        // Method 2 (Fallback): If the sum returns 0 but you have successful conversions,
        // dynamically fetch the active program rate or use a default flat reward rate (e.g., $5.00)
        if ($totalEarned == 0 && $convertedCount > 0) {
            $activeProgram = DB::table('referral_programs')
                ->where('is_active', true)
                ->latest()
                ->first();

            $rewardPerUser = $activeProgram->referrer_reward ?? 5.00; 
            $totalEarned = $convertedCount * $rewardPerUser;
        }

        // 🟢 ១. គណនាទឹកប្រាក់សរុបដែលបានយកទៅកាត់ទូទាត់ទិញទំនិញរួចរាល់ (Total Spent)
        $totalSpent = \App\Models\Order::where('user_id', $user->id)->sum('referral_amount_used');

        // 🟢 ២. គណនាទឹកប្រាក់សំណល់ពិតប្រាកដដែលនៅសល់ក្នុងកាបូប (Available Balance)
        $referralBalance = max(0, $totalEarned - $totalSpent);

        // 🟢 ៣. បន្ថែម 'referral_balance' ទៅក្នុងអារេ $stats ដើម្បីបោះទៅកាន់ View
        $stats = [
            'total_referred'   => $totalReferred,
            'converted'        => $convertedCount,
            'pending'          => $pendingCount,
            'total_earned'     => $totalEarned,
            'referral_balance' => $referralBalance, // លុយសំណល់ជាក់ស្តែង
        ];

        $referralLink = route('register', ['ref' => $myReferral->referral_link_code]);

        return view('frontend.referrals.dashboard', compact('myReferral', 'stats', 'referralLink'));
    }

    /** 
     * List of all referrals made by this user with status 
     */
    public function myReferrals()
    {
        $referrals = Auth::user()->referralsMade()->with('referee')->latest()->paginate(15);
        return view('frontend.referrals.list', compact('referrals'));
    }

    /** 
     * Generate a fresh referral link (e.g. for sharing on different channels) 
     */
    public function generateNewLink()
    {
        $referral = $this->referralService->generateLink(Auth::user());
        return back()->with('success', 'New referral link generated: ' . $referral->referral_link_code);
    }
}