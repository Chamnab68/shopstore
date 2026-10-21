<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlySummaryCommand extends Command
{
    protected $signature = 'report:monthly';
    protected $description = 'Send monthly sales summary report';

    public function handle(NotificationService $notification): void
    {
        $this->info('Collecting monthly data...');

        $now   = Carbon::now();
        $start = $now->copy()->startOfMonth();
        $end   = $now->copy()->endOfMonth();

        $orders = Order::whereBetween('created_at', [$start, $end])->get();

        $totalOrders      = $orders->count();
        $completedOrders  = $orders->where('status', 'delivered')->count();
        $cancelledOrders  = $orders->where('status', 'cancelled')->count();
        $pendingOrders    = $orders->whereNotIn('status', ['delivered', 'cancelled'])->count();
        $totalRevenue     = $orders->sum('total');                                          // ✅ កែ
        $paidRevenue      = $orders->where('payment_status', 'paid')->sum('total');         // ✅ កែ
        $newCustomers     = User::whereBetween('created_at', [$start, $end])->count();

        $topProduct = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->first();

        $productsSold = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->sum('order_items.quantity');

        $data = [
            'month'            => $now->format('F Y'),
            'start'            => $start,
            'end'              => $end,
            'total_orders'     => $totalOrders,
            'completed_orders' => $completedOrders,
            'cancelled_orders' => $cancelledOrders,
            'pending_orders'   => $pendingOrders,
            'total_revenue'    => $totalRevenue,
            'paid_revenue'     => $paidRevenue,
            'new_customers'    => $newCustomers,
            'products_sold'    => $productsSold,
            'top_product'      => $topProduct?->name ?? 'N/A',
        ];

        $notification->monthlySummary($data);

        $this->info('✅ Monthly report sent successfully!');
    }
}