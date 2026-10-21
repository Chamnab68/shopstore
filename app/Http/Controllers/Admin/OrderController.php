<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PointsRule;
use App\Models\PointsTransaction;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function __construct(private NotificationService $notification) {}

    public function index(Request $request)
    {
        $query = Order::with(['user', 'items'])->latest();
        
        if ($request->status) $query->where('status', $request->status);
        if ($request->payment_status) $query->where('payment_status', $request->payment_status);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhere('shipping_name', 'like', '%' . $request->search . '%')
                  ->orWhere('shipping_phone', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->date_from) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->date_to)   $query->whereDate('created_at', '<=', $request->date_to);

        $orders = $query->paginate(20)->appends($request->all());
        
        $stats = [
            'total'     => Order::count(),
            'pending'   => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
        ];
        
        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items.product', 'approvedBy'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled'
        ]);
        
        $order = Order::findOrFail($id);
        $oldStatus = $order->status;

        DB::transaction(function () use ($request, $order, $oldStatus) {
            $order->update([
                'status'      => $request->status,
                'admin_notes' => $request->admin_notes,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            if ($request->payment_status) {
                $order->update(['payment_status' => $request->payment_status]);
            }

            // ⚡ ផ្តល់ពិន្ទុដោយស្វ័យប្រវត្តិតែក្នុងករណី Status ប្តូរទៅជា 'delivered' ថ្មីប៉ុណ្ណោះ
            if ($order->status === 'delivered' && $oldStatus !== 'delivered') {
                $this->awardLoyaltyPoints($order);
            }
        });

        try { 
            $this->notification->orderApproved($order); 
        } catch (\Exception $e) {}

        return response()->json(['success' => true, 'message' => 'Order updated successfully.']);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'order_ids' => 'required|array'
        ]);
        
        $orders = Order::whereIn('id', $request->order_ids)->get();

        DB::transaction(function () use ($orders, $request) {
            foreach ($orders as $order) {
                $oldStatus = $order->status;

                match($request->action) {
                    'confirm' => $order->update(['status' => 'confirmed']),
                    'deliver' => $order->update(['status' => 'delivered']),
                    'cancel'  => $order->update(['status' => 'cancelled']),
                    default   => null,
                };

                // ⚡ ផ្តល់ពិន្ទុផងដែរសម្រាប់ការធ្វើ Bulk Action ទៅជា 'delivered'
                if ($order->status === 'delivered' && $oldStatus !== 'delivered') {
                    $this->awardLoyaltyPoints($order);
                }
            }
        });

        return response()->json(['success' => true, 'message' => count($orders) . ' orders updated.']);
    }

    /**
     * ⚡ មុខងារជំនួយសម្រាប់គណនា និងបូកពិន្ទុជូន Customer
     */
    private function awardLoyaltyPoints(Order $order)
    {
        $user = $order->user;

        // បើគ្មានព័ត៌មាន User ឬ Order នេះធ្លាប់បូកពិន្ទុរួចហើយ គឺមិនបូកទៀតឡើយ
        if (!$user || $order->points_awarded) {
            return;
        }

        // រកមើលឈ្មោះថ្ងៃបច្ចុប្បន្នជាអក្សរតូច (ឧទាហរណ៍៖ 'saturday', 'sunday'...)
        $todayName = strtolower(Carbon::now()->format('l'));

        // ១. ស្វែងរក Rule ពិសេសណាដែលត្រូវនឹងថ្ងៃនេះ
        $activeRule = PointsRule::where('active_days', 'like', '%' . $todayName . '%')
            ->orderBy('priority', 'desc')
            ->first();

        // ២. បើគ្មាន Rule ពិសេសតាមថ្ងៃទេ យកច្បាប់ទូទៅ (Standard Rate/Earn Rate)
        if (!$activeRule) {
            $activeRule = PointsRule::where('type', 'earn rate')->first();
        }

        if ($activeRule) {
            // គណនាពិន្ទុ៖ (តម្លៃសរុប x ពិន្ទុក្នុងមួយដុល្លារ) x មេគុណថ្ងៃពិសេស
            $basePoints = $order->total * $activeRule->points_per_dollar;
            $finalPoints = (int) round($basePoints * $activeRule->multiplier);

            if ($finalPoints > 0) {
                // 🟢 ជំហានទី ១៖ បន្ថែមពិន្ទុទៅកាន់គណនី Customer ក្នុង Table users រួច refresh ទិន្នន័យថ្មីភ្លាមៗ
                $user->increment('points', $finalPoints);
                $user->refresh(); 

                // 🟢 ជំហានទី ២៖ យកតម្លៃពិន្ទុបច្ចុប្បន្នពិតប្រាកដដែលបានបូករួចមកហៅប្រើ (លែងលោត Error 'balance_after')
                $balanceAfter = $user->points ?? 0;

                // បង្កើតប្រវត្តិពិន្ទុក្នុង Table points_transactions
                PointsTransaction::create([
                    'user_id'       => $user->id,
                    'order_id'      => $order->id,
                    'points'        => $finalPoints,
                    'type'          => 'earn',
                    'balance_after' => $balanceAfter, // 🟢 បញ្ជូនទិន្នន័យពិតប្រាកដទៅ Database
                ]);

                // ចំណាំថា Order នេះត្រូវបានបូកពិន្ទុរួចរាល់ហើយ
                $order->update(['points_awarded' => true]);
            }
        }
    }

    /**
     * ⚡ មុខងារពិសេសសម្រាប់ Sync ពិន្ទុពីបណ្តា Order ចាស់ៗ (ស្របតាម Route /sync-past-points)
     */
    public function syncPastPoints()
    {
        $orders = Order::where('status', 'delivered')
            ->where('points_awarded', false)
            ->get();

        $count = 0;

        foreach ($orders as $order) {
            $user = $order->user;
            if (!$user) continue;

            // គណនាតាមរូបមន្តស្តង់ដារ
            $activeRule = PointsRule::where('type', 'earn rate')->first();
            $pointsPerDollar = $activeRule ? $activeRule->points_per_dollar : 1;
            $multiplier = $activeRule ? $activeRule->multiplier : 1;

            $finalPoints = (int) round(($order->total * $pointsPerDollar) * $multiplier);

            if ($finalPoints > 0) {
                // បូកបញ្ចូលទៅក្នុង Table users
                $user->increment('points', $finalPoints);
                $user->refresh();

                // បង្កើតប្រវត្តិការប្តូរប្រាក់ និងដោះស្រាយបញ្ហា balance_after
                PointsTransaction::create([
                    'user_id'       => $user->id,
                    'order_id'      => $order->id,
                    'points'        => $finalPoints,
                    'type'          => 'earn',
                    'balance_after' => $user->points ?? 0,
                ]);

                $order->update(['points_awarded' => true]);
                $count++;
            }
        }

        return "{$count} orders have been successfully awarded loyalty points!";
    }
}