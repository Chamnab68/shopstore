<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Order, Product, User, Category, OrderItem, Role};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function salesSummary(Request $request)
    {
        $from = $request->date_from ?? now()->startOfMonth()->toDateString();
        $to   = $request->date_to   ?? now()->toDateString();
        $data = Order::whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('SUM(discount) as discounts')
            )
            ->groupBy('date')->orderBy('date')->get();
        return view('admin.reports.sales_summary', compact('data','from','to'));
    }

    public function revenueByCategory(Request $request)
    {
        $from = $request->from_date ?? now()->startOfMonth()->toDateString();
        $to   = $request->to_date   ?? now()->toDateString();

        $data = Category::select('categories.*',
                DB::raw('SUM(order_items.subtotal) as revenue'),
                DB::raw('SUM(order_items.quantity) as qty'))
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->groupBy('categories.id')
            ->orderByDesc('revenue')
            ->get();

        return view('admin.reports.revenue_by_category', compact('data', 'from', 'to'));
    }

    public function topProducts(Request $request)
    {
        $limit = $request->limit ?? 20;

        $query = Product::select('products.*',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as revenue'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->with('category');

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('orders.created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        $data = $query->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->take($limit)
            ->get();

        return view('admin.reports.top_products', compact('data', 'limit'));
    }

    public function customerReport(Request $request)
    {
        $query = User::with('tier') 
            ->select(
                'users.id', 'users.name', 'users.email', 'users.phone', 
                'users.points_balance', 'users.tier_id', 'users.created_at'
            )
            ->leftJoin('orders', 'users.id', '=', 'orders.user_id')
            ->selectRaw('COUNT(orders.id) as order_count')
            ->selectRaw('COALESCE(SUM(orders.total), 0) as lifetime_value');

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('users.created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        $data = $query->groupBy(
                'users.id', 'users.name', 'users.email', 'users.phone', 
                'users.points_balance', 'users.tier_id', 'users.created_at'
            )
            ->orderByDesc('lifetime_value')
            ->paginate(20)
            ->appends($request->all());

        $roles = Role::all();

        return view('admin.reports.customers', compact('data', 'roles'));
    }

    public function assignRole(Request $request, $id)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'role_id' => $request->role_id
        ]);

        return redirect()->back()->with('success', 'Role assigned successfully.');
    }

    public function orderStatus(Request $request)
    {
        $query = Order::select(
            'status',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total) as total')
        );

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        $data = $query->groupBy('status')->get();

        return view('admin.reports.order_status', compact('data'));
    }

    public function paymentReport(Request $request)
    {
        $from = $request->date_from ?? now()->startOfMonth()->toDateString();
        $to   = $request->date_to   ?? now()->toDateString();
        $data = Order::select('payment_method', 'payment_status',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total'))
            ->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->groupBy('payment_method', 'payment_status')->get();
        return view('admin.reports.payment', compact('data','from','to'));
    }

    public function stockReport(Request $request)
    {
        $query = Product::with('category');
        if ($request->filter === 'low')      $query->where('stock', '<', 10)->where('stock', '>', 0);
        elseif ($request->filter === 'out')  $query->where('stock', 0);
        $data = $query->orderBy('stock')->paginate(20);
        return view('admin.reports.stock', compact('data'));
    }

    public function monthlyTrend(Request $request)
    {
        $query = Order::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->where('payment_status', 'paid');

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        } else {
            $query->where('created_at', '>=', now()->subYear());
        }

        $data = $query->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')->get();

        return view('admin.reports.monthly_trend', compact('data'));
    }

    public function customerRetention()
    {
        $data = DB::select("
            SELECT
                DATE_FORMAT(o.created_at,'%Y-%m') as month,
                COUNT(DISTINCT CASE WHEN o.id = fo.first_id THEN o.user_id END) as new_customers,
                COUNT(DISTINCT CASE WHEN o.id != fo.first_id THEN o.user_id END) as returning_customers
            FROM orders o
            LEFT JOIN (SELECT user_id, MIN(id) as first_id FROM orders GROUP BY user_id) fo
                ON o.user_id = fo.user_id
            WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY month ORDER BY month
        ");
        return view('admin.reports.customer_retention', compact('data'));
    }

    public function deliveryPerformance(Request $request)
    {
        $query = Order::select(
            'status',
            DB::raw('COUNT(*) as count'),
            DB::raw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
        )->whereIn('status', ['delivered', 'shipped', 'cancelled']);

        $recentQuery = Order::where('status', 'delivered')->with('user');

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $fromDate = $request->from_date . ' 00:00:00';
            $toDate = $request->to_date . ' 23:59:59';

            $query->whereBetween('created_at', [$fromDate, $toDate]);
            $recentQuery->whereBetween('created_at', [$fromDate, $toDate]);
        }

        $data = $query->groupBy('status')->get();
        $recentDelivered = $recentQuery->latest()->take(20)->get();

        return view('admin.reports.delivery', compact('data', 'recentDelivered'));
    }

    public function exportPdf($type, Request $request)
{
    $viewName = match($type) {
        'top-products'     => 'top-products',
        'revenue-category' => 'revenue',
        'order-status'     => 'order-status',
        'payment'          => 'payment', // ➕ កំណត់ឈ្មោះ View សម្រាប់ Payment Report
        default            => $type,
    };

    $viewData = match($type) {
        'sales' => ['data' => Order::latest()->take(100)->get()],
        'products' => ['data' => Product::with('category')->take(100)->get()],
        'revenue-category' => [
            'data' => Category::leftJoin('products', 'categories.id', '=', 'products.category_id')
                ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                ->select(
                    'categories.name',
                    \DB::raw('SUM(order_items.quantity) as qty'),
                    \DB::raw('SUM(order_items.price * order_items.quantity) as revenue')
                )
                ->groupBy('categories.id', 'categories.name')
                ->get()
        ],
        'top-products' => [
            'data' => Product::with('category')
                ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                ->select(
                    'products.*',
                    \DB::raw('SUM(order_items.quantity) as total_sold'),
                    \DB::raw('SUM(order_items.price * order_items.quantity) as revenue')
                )
                ->groupBy('products.id')
                ->orderByDesc('total_sold')
                ->take(50)
                ->get()
        ],
        'order-status' => [
            'data' => Order::select(
                    'status',
                    \DB::raw('COUNT(id) as count'),
                    \DB::raw('SUM(total) as total')
                )
                ->groupBy('status')
                ->get()
        ],

        // ➕ បន្ថែម Query សម្រាប់ Payment Report ត្រង់នេះ
        'payment' => [
            'data' => Order::select(
                    'payment_method',
                    'payment_status',
                    \DB::raw('COUNT(id) as count'),
                    \DB::raw('SUM(total) as total')
                )
                ->groupBy('payment_method', 'payment_status')
                ->get()
        ],

        default => ['data' => collect()],
    };

    $pdf = Pdf::loadView("admin.reports.pdf.{$viewName}", $viewData)->setPaper('a4');
    
    return $pdf->download("shopstore-report-{$type}-".now()->format('Y-m-d').'.pdf');
}

    public function recalculateLoyalty()
    {
        $users = User::all();
        $updatedCount = 0;

        foreach($users as $user) {
            $lifetimeValue = Order::where('user_id', $user->id)->sum('total');
            
            if ($lifetimeValue > 0) {
                $calculatedPoints = round($lifetimeValue); 
                
                $matchedTier = class_exists('\App\Models\Tier') 
                    ? \App\Models\Tier::where('min_points', '<=', $calculatedPoints)->orderBy('level', 'desc')->first()
                    : null;
                                 
                $user->update([
                    'points_balance'  => $calculatedPoints,
                    'lifetime_points' => $calculatedPoints,
                    'tier_id'         => $matchedTier ? $matchedTier->id : null
                ]);
                
                $updatedCount++;
            }
        }
        
        return "🎉 បានធ្វើបច្ចុប្បន្នភាពទិន្នន័យ Point & Tier ជូនអតិថិជនចំនួន " . $updatedCount . " នាក់ជោគជ័យ!";
    }
}