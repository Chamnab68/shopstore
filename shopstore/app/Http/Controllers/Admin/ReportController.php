<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Order, Product, User, Category, OrderItem};
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
        $from = $request->date_from ?? now()->startOfMonth()->toDateString();
        $to   = $request->date_to   ?? now()->toDateString();
        $data = Category::select('categories.*',
                DB::raw('SUM(order_items.subtotal) as revenue'),
                DB::raw('SUM(order_items.quantity) as qty'))
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->groupBy('categories.id')->orderByDesc('revenue')->get();
        return view('admin.reports.revenue_by_category', compact('data','from','to'));
    }

    public function topProducts(Request $request)
    {
        $limit = $request->limit ?? 20;
        $data  = Product::select('products.*',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as revenue'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->with('category')
            ->groupBy('products.id')->orderByDesc('total_sold')->take($limit)->get();
        return view('admin.reports.top_products', compact('data','limit'));
    }

    public function customerReport(Request $request)
    {
        $data = User::where('role', 'customer')
            ->select('users.*',
                DB::raw('COUNT(orders.id) as order_count'),
                DB::raw('COALESCE(SUM(orders.total),0) as lifetime_value'))
            ->leftJoin('orders', 'users.id', '=', 'orders.user_id')
            ->groupBy('users.id')->orderByDesc('lifetime_value')->paginate(20);
        return view('admin.reports.customers', compact('data'));
    }

    public function orderStatus(Request $request)
    {
        $data = Order::select('status',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total'))
            ->groupBy('status')->get();
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

    public function monthlyTrend()
    {
        $data = Order::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders'))
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('year', 'month')
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
        $data = Order::select('status',
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(DATEDIFF(updated_at, created_at)) as avg_days'))
            ->whereIn('status', ['delivered','shipped','cancelled'])
            ->groupBy('status')->get();
        $recentDelivered = Order::where('status', 'delivered')->with('user')->latest()->take(20)->get();
        return view('admin.reports.delivery', compact('data','recentDelivered'));
    }

    public function exportPdf($type, Request $request)
    {
        $viewData = match($type) {
            'sales'    => ['data' => Order::latest()->take(100)->get()],
            'products' => ['data' => Product::with('category')->take(100)->get()],
            default    => ['data' => collect()],
        };
        $pdf = Pdf::loadView("admin.reports.pdf.{$type}", $viewData)->setPaper('a4');
        return $pdf->download("shopstore-report-{$type}-".now()->format('Y-m-d').'.pdf');
    }
}
