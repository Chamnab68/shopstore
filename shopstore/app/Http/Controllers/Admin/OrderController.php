<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(private NotificationService $notification) {}

    public function index(Request $request)
    {
        $query = Order::with(['user','items'])->latest();

        if ($request->status) $query->where('status', $request->status);
        if ($request->payment_status) $query->where('payment_status', $request->payment_status);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('order_number','like','%'.$request->search.'%')
                  ->orWhere('shipping_name','like','%'.$request->search.'%')
                  ->orWhere('shipping_phone','like','%'.$request->search.'%');
            });
        }
        if ($request->date_from) $query->whereDate('created_at','>=',$request->date_from);
        if ($request->date_to)   $query->whereDate('created_at','<=',$request->date_to);

        $orders = $query->paginate(20)->appends($request->all());
        $stats = [
            'total'      => Order::count(),
            'pending'    => Order::where('status','pending')->count(),
            'confirmed'  => Order::where('status','confirmed')->count(),
            'delivered'  => Order::where('status','delivered')->count(),
        ];

        return view('admin.orders.index', compact('orders','stats'));
    }

    public function show($id)
    {
        $order = Order::with(['user','items.product','approvedBy'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:confirmed,processing,shipped,delivered,cancelled']);
        $order = Order::findOrFail($id);
        $order->update([
            'status'      => $request->status,
            'admin_notes' => $request->admin_notes,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        if ($request->payment_status) {
            $order->update(['payment_status' => $request->payment_status]);
        }

        try { $this->notification->orderApproved($order); } catch (\Exception $e) {}

        return response()->json(['success' => true, 'message' => 'Order updated successfully.']);
    }

    public function bulkAction(Request $request)
    {
        $request->validate(['action' => 'required|string', 'order_ids' => 'required|array']);
        $orders = Order::whereIn('id', $request->order_ids)->get();

        foreach ($orders as $order) {
            match($request->action) {
                'confirm'  => $order->update(['status' => 'confirmed']),
                'deliver'  => $order->update(['status' => 'delivered']),
                'cancel'   => $order->update(['status' => 'cancelled']),
                default    => null,
            };
        }

        return response()->json(['success' => true, 'message' => count($orders) . ' orders updated.']);
    }
}
