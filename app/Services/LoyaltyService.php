<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoyaltyService
{
    /**
     * គណនាពិន្ទុផ្អែកលើ Rules និងកត់ត្រាចូលទាំងក្នុង Users Table និង Activity Logs
     */
    public function processOrderLoyalty(Order $order)
    {
        try {
            // ទាញយកព័ត៌មាន User ដែលជាអ្នកទិញ
            $user = $order->user ?? User::find($order->user_id);
            if (!$user) return;

            $currentDate = now()->format('Y-m-d');

            // ១. 🔍 ស្កែនស្វែងរកច្បាប់ប្រមូលពិន្ទុ (Points Rules) ដែល Active ត្រូវនឹងថ្ងៃ និងកាលបរិច្ឆេទនេះ
            $rule = DB::table('points_rules')
                ->where('status', 'Active')
                ->whereRaw("FIND_IN_SET(LOWER(?), LOWER(active_days))", [now()->dayName])
                ->whereDate('start_date', '<=', $currentDate)
                ->whereDate('end_date', '>=', $currentDate)
                ->orderBy('priority', 'desc')
                ->first();

            // បើសិនជាគ្មានច្បាប់ពិសេសទេ គឺយកលំនាំដើម (1$ = 1 point, មេគុណ 1.0x)
            $pointsPerDollar = $rule ? (float)$rule->points_per_dollar : 1.00;
            $multiplier      = $rule ? (float)$rule->multiplier : 1.0;

            // ២. 🧮 គណនាពិន្ទុតាមរូបមន្ត៖ ទឹកប្រាក់សរុប * points_per_dollar * multiplier
            $pointsEarned = (int) floor($order->total * $pointsPerDollar * $multiplier); 

            if ($pointsEarned > 0) {
                DB::transaction(function () use ($user, $pointsEarned, $order, $rule) {
                    
                    // ៣. 🟢 បើកកូដ និងបូកពិន្ទុចូល Column ត្រឹមត្រូវ (យោងតាមរូបភាព image_a1467a.jpg)
                    $user->increment('points_balance', $pointsEarned);
                    $user->increment('lifetime_points', $pointsEarned);

                    // ៤. 🪵 បញ្ចូលទិន្នន័យទៅក្នុងតារាង activity_logs ឱ្យត្រូវតាម Schema
                    DB::table('activity_logs')->insert([
                        'user_id'      => $user->id,
                        'action'       => 'points.earned',
                        'subject_type' => 'App\Models\Order',
                        'subject_id'   => $order->id,
                        'old_values'   => null,
                        'new_values'   => json_encode([
                            'order_number' => $order->order_number,
                            'total_amount' => $order->total,
                            'points_added' => $pointsEarned,
                            'applied_rule' => $rule ? $rule->name : 'Standard Rate',
                            'multiplier'   => $rule ? $rule->multiplier . 'x' : '1.0x',
                        ]),
                        'metadata'     => null,
                        'ip_address'   => request()->ip() ?? '127.0.0.1',
                        'user_agent'   => request()->userAgent() ?? 'Unknown',
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);

                    // ៥. 🪙 (ជម្រើសបន្ថែម) បើបងចង់កត់ត្រាចូលតារាង points_transactions ផ្ទាល់ខ្លួនរបស់បង
                    if (Schema::hasTable('points_transactions')) {
                        DB::table('points_transactions')->insert([
                            'user_id'     => $user->id,
                            'points'      => $pointsEarned,
                            'type'        => 'earn', 
                            'description' => "Earned {$pointsEarned} points from Order #" . $order->order_number,
                            'created_at'  => now(),
                            'updated_at'  => now(),
                        ]);
                    }
            });

                Log::info("Loyalty System: Inserted points.earned log & updated balance for User ID: {$user->id}. Points: {$pointsEarned}");
            }

            // ពិនិត្យលក្ខខណ្ឌប្តូរលំដាប់ថ្នាក់ (Tiers)
            $this->checkAndUpgradeTier($user, $order);

            // ពិនិត្យលក្ខខណ្ឌកម្មវិធីណែនាំមិត្តភក្តិ (Referral Program)
            $this->processReferralReward($order, $user);

        } catch (\Exception $e) {
            Log::error("Loyalty Processing Error: " . $e->getMessage());
        }
    }

    private function checkAndUpgradeTier($user, $order)
    {
        // លក្ខខណ្ឌ Tier ស្វ័យប្រវត្តិ...
    }

    private function processReferralReward(Order $order, $user)
    {
        // លក្ខខណ្ឌ Referral Program...
    }
}