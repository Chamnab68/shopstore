<?php

namespace App\Http\Controllers;

use App\Models\{Category, Product, Banner, Badge, Tier};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('is_active', true)->orderBy('sort_order')->get();
        $categories = Category::where('is_active', true)->withCount('products')->orderBy('sort_order')->get();
        $featuredProducts = Product::where('is_active', true)->featured()->with('category')->take(8)->get();
        $editorPicks = Product::where('is_active', true)->with('category')->orderBy('rating','desc')->take(8)->get();
        $bestSellers = Product::where('is_active', true)->with('category')->orderBy('sold_count','desc')->take(6)->get();
        $saleProducts = Product::where('is_active', true)->whereNotNull('sale_price')->with('category')->take(6)->get();

        $myBadges = collect(); // បង្កើត Collection ទទេមួយទុកជាមុនការពារកុំឱ្យទំព័រ View លោត Error ពេលមិនទាន់ Login

        if (Auth::check()) {
            $user = Auth::user();

            // ── 💡 ផ្នែកស្កេនពិនិត្យ និងផ្តល់មេដាយស្វ័យប្រវត្តិ ──
            // រាប់ចំនួន Order របស់គាត់ដែលមានស្ថានភាពជោគជ័យ
            $orderCount = $user->orders()
                ->whereIn('status', ['completed', 'delivered', 'confirmed', 'Delivered', 'Confirmed'])
                ->count();

            if ($orderCount >= 1) {
                // 💡 ស្វែងរកតាមរយៈ Column 'name' តែមួយគត់ដែលប្រាកដជាមាននៅក្នុងតារាង badges របស់បង
                $badge = Badge::where('name', 'First Purchase')->first();

                if ($badge) {
                    // ពិនិត្យមើលថាតើគាត់មានមេដាយនេះរួចហើយឬនៅ
                    $alreadyHasBadge = DB::table('user_badges')
                        ->where('user_id', $user->id)
                        ->where('badge_id', $badge->id)
                        ->exists();

                    // ប្រសិនបើធ្លាប់ទិញហើយ តែមិនទាន់មានទិន្នន័យមេដាយ នោះប្រព័ន្ធនឹងបង្កើតជូនភ្លាមៗ
                    if (!$alreadyHasBadge) {
                        DB::table('user_badges')->insert([
                            'user_id'      => $user->id,
                            'badge_id'     => $badge->id,
                            'awarded_at'   => now(),
                            'is_displayed' => true,
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ]);
                    }
                }
            }

            // <b>ទាញយកមេដាយរបស់គាត់ពី Database មកបង្ហាញនៅលើ Frontend</b>
            $myBadges = $user->badges()->latest()->get();
        }

        return view('frontend.home', compact(
            'banners',
            'categories',
            'featuredProducts',
            'editorPicks',
            'bestSellers',
            'saleProducts',
            'myBadges' // បោះ variable នេះទៅកាន់ទំព័រ view ជាមួយគ្នា
        ));
    }

    /**
     * 🟢 មុខងារបង្ហាញទំព័រ Rewards (Loyalty Program) សម្រាប់អតិថិជន
     * កែតម្រូវទីតាំង View ទៅកាន់ 'frontend.rewards.index' ឱ្យបានត្រឹមត្រូវ
     */
    public function rewards()
    {
        $user = auth()->user();
        
        // ទាញយកបញ្ជីកាដូដែលសកម្ម
        $rewards = \App\Models\Reward::where('is_active', true)->latest()->get();
        
        // ទាញយកប្រវត្តិកាដូដែល Customer នេះធ្លាប់បានប្តូរ (កែពី 'redeem' មក 'spend' ឱ្យត្រូវតាម DB)
        $myRedemptions = \App\Models\PointsTransaction::where('user_id', auth()->id())
            ->where('type', 'spend')
            ->latest()
            ->get();

        // បើសិនជាប្រព័ន្ធបងមានប្រើប្រាស់ Tiers (កម្រិតសមាជិក)
        $tiers = \App\Models\Tier::orderBy('level', 'asc')->get() ?? collect();
        $currentTier = $user->tier ?? \App\Models\Tier::orderBy('level', 'asc')->first();

        // 🟢 កែសម្រួល៖ ហៅទៅកាន់ Folder rewards និង file index.blade.php ឱ្យចំទីតាំងពិតប្រាកដ
        return view('frontend.rewards.index', compact('user', 'rewards', 'myRedemptions', 'tiers', 'currentTier'));
    }

    /**
     * 🟢 មុខងារចុច Redeem កាត់ពិន្ទុអតិថិជន
     */
    public function redeemReward($id)
    {
        $user = Auth::user();
        $reward = \App\Models\Reward::find($id);

        if (!$reward) {
            return back()->with('error', 'រកមិនឃើញរង្វាន់នេះទេ!');
        }

        if ($user->points_balance < $reward->point_cost) {
            return back()->with('error', 'ពិន្ទុរបស់អ្នកមិនគ្រប់គ្រាន់ទេ!');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($user, $reward) {
            
            // ១. កាត់ពិន្ទុ User (ផ្អែកលើ column ពិតរបស់អ្នក អាចជា points ឬ points_balance)
            $user->decrement('points_balance', $reward->point_cost);

            // ២. កាត់ស្តុក
            $reward->decrement('total_stock', 1);
            $reward->increment('redeemed_count', 1);

            // ៣. បង្កើតប្រវត្តិចូលទៅក្នុង Table reward_redemptions (កន្លែងនេះហើយដែលធ្វើឱ្យ Admin ឃើញ)
            \App\Models\RewardRedemption::create([
                'user_id'         => $user->id,
                'reward_id'       => $reward->id,
                'points_spent'    => $reward->point_cost,
                'redemption_code' => 'RDM-' . strtoupper(\Illuminate\Support\Str::random(8)),
                'status'          => 'pending',
            ]);

            // ៤. បន្ថែមចូល points_transactions ផង (បើមាន)
           // ៤. បន្ថែមចូល points_transactions ផង (បើមាន)
            if (class_exists(\App\Models\PointsTransaction::class)) {
                \App\Models\PointsTransaction::create([
                    'user_id'       => $user->id,
                    'points'        => -$reward->point_cost,
                    'type'          => 'redeem',
                    'balance_after' => $user->points_balance, // 👈 បន្ថែមបន្ទាត់នេះ ដើម្បីដោះស្រាយ Error Field 'balance_after'
                    'description'   => 'Redeemed reward: ' . $reward->name,
                ]);
            }
        });

        return back()->with('success', 'ការប្តូររង្វាន់បានជោគជ័យ!');
    }

    public function about() { return view('frontend.about'); }
    public function contact() { return view('frontend.contact'); }
}