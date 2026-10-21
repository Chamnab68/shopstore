<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Order, Product, User, Category};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders'    => Order::count(),
            'total_revenue'   => Order::where('payment_status','paid')->sum('total'),
            'total_customers' => User::where('role','customer')->count(),
            'total_products'  => Product::count(),
            'pending_orders'  => Order::where('status','pending')->count(),
            'low_stock'       => Product::where('stock','<',10)->count(),
            'today_orders'    => Order::whereDate('created_at',today())->count(),
            'today_revenue'   => Order::whereDate('created_at',today())->where('payment_status','paid')->sum('total'),
            
        ];

        $recentOrders = Order::with('user')->latest()->take(10)->get();

        $monthlySales = Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(total) as total'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at','>=',now()->subMonths(12))
        ->where('payment_status','paid')
        ->groupBy('year','month')
        ->orderBy('year')->orderBy('month')
        ->get();

        $topProducts = Product::select('products.*', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('order_items','products.id','=','order_items.product_id')
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->take(5)->get();

        $categoryRevenue = Category::select('categories.*', DB::raw('SUM(order_items.subtotal) as revenue'))
            ->join('products','categories.id','=','products.category_id')
            ->join('order_items','products.id','=','order_items.product_id')
            ->groupBy('categories.id')
            ->orderByDesc('revenue')
            ->take(6)->get();

        return view('admin.dashboard', compact('stats','recentOrders','monthlySales','topProducts','categoryRevenue'));
    }
}

// ============================================================
// ReportController
// ============================================================
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Order, Product, User, Category, OrderItem};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    // Report 1: Sales Summary
    public function salesSummary(Request $request)
    {
        $from = $request->date_from ?? now()->startOfMonth()->toDateString();
        $to   = $request->date_to   ?? now()->toDateString();
        $data = Order::whereBetween('created_at',[$from.' 00:00:00',$to.' 23:59:59'])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as orders'), DB::raw('SUM(total) as revenue'), DB::raw('SUM(discount) as discounts'))
            ->groupBy('date')->orderBy('date')->get();
        return view('admin.reports.sales_summary', compact('data','from','to'));
    }

    // Report 2: Revenue by Category
    public function revenueByCategory(Request $request)
    {
        $from = $request->date_from ?? now()->startOfMonth()->toDateString();
        $to   = $request->date_to   ?? now()->toDateString();
        $data = Category::select('categories.*', DB::raw('SUM(order_items.subtotal) as revenue'), DB::raw('SUM(order_items.quantity) as qty'))
            ->join('products','categories.id','=','products.category_id')
            ->join('order_items','products.id','=','order_items.product_id')
            ->join('orders','order_items.order_id','=','orders.id')
            ->whereBetween('orders.created_at',[$from.' 00:00:00',$to.' 23:59:59'])
            ->groupBy('categories.id')->orderByDesc('revenue')->get();
        return view('admin.reports.revenue_by_category', compact('data','from','to'));
    }

    // Report 3: Top Products
    public function topProducts(Request $request)
    {
        $limit = $request->limit ?? 20;
        $data = Product::select('products.*', DB::raw('SUM(order_items.quantity) as total_sold'), DB::raw('SUM(order_items.subtotal) as revenue'))
            ->join('order_items','products.id','=','order_items.product_id')
            ->groupBy('products.id')->orderByDesc('total_sold')->take($limit)->get();
        return view('admin.reports.top_products', compact('data','limit'));
    }

    // Report 4: Customer Report
    public function customerReport(Request $request)
    {
        $data = User::where('role','customer')
            ->select('users.*', DB::raw('COUNT(orders.id) as order_count'), DB::raw('SUM(orders.total) as lifetime_value'))
            ->leftJoin('orders','users.id','=','orders.user_id')
            ->groupBy('users.id')->orderByDesc('lifetime_value')->paginate(20);
        return view('admin.reports.customers', compact('data'));
    }

    // Report 5: Order Status Report
    public function orderStatus(Request $request)
    {
        $data = Order::select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('status')->get();
        return view('admin.reports.order_status', compact('data'));
    }

    // Report 6: Payment Report
    public function paymentReport(Request $request)
    {
        $from = $request->date_from ?? now()->startOfMonth()->toDateString();
        $to   = $request->date_to   ?? now()->toDateString();
        $data = Order::select('payment_method','payment_status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->whereBetween('created_at',[$from.' 00:00:00',$to.' 23:59:59'])
            ->groupBy('payment_method','payment_status')->get();
        return view('admin.reports.payment', compact('data','from','to'));
    }

    // Report 7: Stock Report
    public function stockReport(Request $request)
    {
        $query = Product::with('category');
        if ($request->filter === 'low') $query->where('stock','<',10);
        elseif ($request->filter === 'out') $query->where('stock',0);
        $data = $query->orderBy('stock')->paginate(20);
        return view('admin.reports.stock', compact('data'));
    }

    // Report 8: Monthly Revenue Trend
    public function monthlyTrend()
    {
        $data = Order::select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total) as revenue'), DB::raw('COUNT(*) as orders'))
            ->where('payment_status','paid')
            ->where('created_at','>=',now()->subYear())
            ->groupBy('year','month')->orderBy('year')->orderBy('month')->get();
        return view('admin.reports.monthly_trend', compact('data'));
    }

    // Report 9: New vs Returning Customers
    public function customerRetention()
    {
        $data = DB::select("
            SELECT
                DATE_FORMAT(o.created_at,'%Y-%m') as month,
                COUNT(DISTINCT CASE WHEN o.id = first_orders.first_id THEN o.user_id END) as new_customers,
                COUNT(DISTINCT CASE WHEN o.id != first_orders.first_id THEN o.user_id END) as returning_customers
            FROM orders o
            LEFT JOIN (
                SELECT user_id, MIN(id) as first_id FROM orders GROUP BY user_id
            ) first_orders ON o.user_id = first_orders.user_id
            WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY month ORDER BY month
        ");
        return view('admin.reports.customer_retention', compact('data'));
    }

    // Report 10: Delivery Performance
    public function deliveryPerformance(Request $request)
    {
        $data = Order::select('status', DB::raw('COUNT(*) as count'), DB::raw('AVG(DATEDIFF(updated_at, created_at)) as avg_days'))
            ->whereIn('status',['delivered','shipped','cancelled'])
            ->groupBy('status')->get();
        $recentDelivered = Order::where('status','delivered')->with('user')->latest()->take(20)->get();
        return view('admin.reports.delivery', compact('data','recentDelivered'));
    }

    // Export PDF
    public function exportPdf($type, Request $request)
    {
        $data = $this->getReportData($type, $request);
        $pdf = Pdf::loadView("admin.reports.pdf.{$type}", $data);
        return $pdf->download("report-{$type}-".now()->format('Y-m-d').'.pdf');
    }

    private function getReportData($type, $request): array
    {
        return match($type) {
            'sales'    => ['data' => Order::latest()->take(100)->get()],
            'products' => ['data' => Product::with('category')->take(100)->get()],
            default    => ['data' => collect()],
        };
    }
}
