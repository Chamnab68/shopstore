<?php

namespace App\Http\Controllers;

use App\Models\{Cart, Order, OrderItem, Setting, Referral, Badge, Tier, LoyaltyActivityLog, PointsTransaction, Product};
use App\Services\{TelegramService, NotificationService, LoyaltyService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Storage, Log};
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderController extends Controller
{
    public function __construct(
        private TelegramService $telegram,
        private NotificationService $notification,
        private LoyaltyService $loyalty 
    ) {}

    // ── Checkout Page ─────────────────────────────────────────────────────────
    public function checkout()
{
    $user = auth()->user();
    $cart = session()->get('cart', []);

    $cartItems = collect();
    $subtotal = 0;

    foreach ($cart as $id => $details) {
        $product = Product::find($id);
        if ($product) {
            $price = $details['price'] ?? ($product->sale_price ?? $product->price);
            $quantity = $details['quantity'] ?? 1;

            $cartItems->put($id, (object)[
                'product' => $product,
                'product_id' => $id,
                'quantity' => $quantity,
                'price' => $price
            ]);
            $subtotal += $price * $quantity;
        }
    }

    $shippingFee = $subtotal > 50 ? 0 : 0.99;
    $total = $subtotal + $shippingFee;

    // 💡 ទាញយកទឹកប្រាក់ពី Column referral_cash_balance ផ្ទាល់តែម្តង
    $referralBalance = $user->referral_cash_balance ?? 0; 

    return view('frontend.checkout.index', compact(
        'cartItems', 
        'subtotal', 
        'shippingFee', 
        'total', 
        'referralBalance'
    ));
}

    // ── Store / Place Order ──────────────────────────────────────────────────
    public function placeOrder(Request $request)
    {
        $request->validate([
            'shipping_name'             => 'required|string|max:100',
            'shipping_phone'            => 'required|string|max:20',
            'shipping_address'          => 'required|string|max:500',
            'notes'                     => 'nullable|string|max:500',
            'payment_method'            => 'required|in:bank_transfer,cash_on_delivery,referral,bakong', 
            'fallback_payment_method'   => 'nullable|in:bank_transfer,cash_on_delivery',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        foreach ($cart as $id => $details) {
            $product = Product::find($id);
            if (!$product || $product->stock < $details['quantity']) {
                return back()->with('error', "Sorry, " . ($product->name ?? 'Product') . " is out of stock.");
            }
        }

        $subtotal = 0;
        foreach ($cart as $details) {
            $subtotal += $details['price'] * $details['quantity'];
        }

        $shippingFee = $subtotal > 50 ? 0 : 0.99; 
        $total = $subtotal + $shippingFee;

        $user = Auth::user();
        
        // ទាញយកទឹកប្រាក់ពិតប្រាកដពី Column referral_cash_balance
        $currentReferralBalance = $user->referral_cash_balance ?? 0;
$paymentMethod = $request->payment_method;
        $referralAmountUsed = 0;
        $finalPaymentMethod = $paymentMethod;

        // 💡 កែសម្រួល Condition ឱ្យចាប់យកគ្រប់ករណីដែលប្រើប្រាស់ Referral Balance
        if ($paymentMethod === 'referral' || str_contains($paymentMethod, 'referral')) {
            
            if ($currentReferralBalance >= $total) {
                $referralAmountUsed = $total;
                $finalPaymentMethod = 'referral_full';
                
                // កាត់កងទឹកប្រាក់ចេញទាំងស្រុង ពេលបង់គ្រប់ ១០០%
                $user->decrement('referral_cash_balance', $total);
            } else {
                // ករណីបង់ប្រាក់ចម្រុះ (Mixed Payment)
                $referralAmountUsed = $currentReferralBalance;
                $fallback = $request->input('fallback_payment_method', 'cash_on_delivery');
                $finalPaymentMethod = 'referral_mixed_' . $fallback;
                
                // 💡 កាត់កងទឹកប្រាក់ referral_cash_balance ឱ្យមកសល់ 0 ជាដាច់ខាត
                $user->update(['referral_cash_balance' => 0]); 
            }
        }

        $remainingPayable = max(0, $total - $referralAmountUsed);

        try {
            $orderId = DB::transaction(function () use ($request, $cart, $subtotal, $shippingFee, $total, $referralAmountUsed, $remainingPayable, $finalPaymentMethod, $currentReferralBalance) {

                $user = Auth::user(); // ហៅ User មកប្រើប្រាស់ផ្ទាល់ក្នុង Transaction

                // 💡 កាត់កងទឹកប្រាក់ Referral ត្រង់នេះភ្លាមៗ
                if ($referralAmountUsed > 0) {
                    if ($finalPaymentMethod === 'referral_full') {
                        $user->decrement('referral_cash_balance', $total);
                    } else {
                        // ករណី Mixed Payment (កាត់លុយដែលមានទាំងអស់ឱ្យសល់ 0)
                        $user->update(['referral_cash_balance' => 0]);
                    }
                }

                $order = Order::create([
                    'order_number'         => Order::generateOrderNumber(),
                    'user_id'              => $user->id,
                    'status'               => Order::STATUS_PENDING,
                    'payment_status'       => Order::PAYMENT_UNPAID,
                    'payment_method'       => $finalPaymentMethod,
                    'subtotal'             => $subtotal,
                    'discount'             => 0,
                    'shipping_fee'         => $shippingFee,
                    'total'                => $total,
                    'referral_amount_used' => $referralAmountUsed,
                    'remaining_payable'    => $remainingPayable,
                    'shipping_name'        => $request->shipping_name,
                    'shipping_phone'       => $request->shipping_phone,
                    'shipping_address'     => $request->shipping_address,
                    'notes'                => $request->notes,
                    'points_awarded'       => false, 
                ]);

                foreach ($cart as $id => $details) {
                    $product = Product::find($id);
                    if ($product) {
                        OrderItem::create([
                            'order_id'      => $order->id,
                            'product_id'    => $id,
                            'product_name'  => $product->name,
                            'product_image' => $product->image,
                            'price'         => $product->price,
                            'sale_price'    => $product->sale_price,
                            'quantity'      => $details['quantity'],
                            'subtotal'      => $details['price'] * $details['quantity'],
                        ]);
                        $product->decrement('stock', $details['quantity']);
                        $product->increment('sold_count', $details['quantity']);
                    }
                }

                session()->forget('cart');

                try {
                    $this->notification->orderPlaced($order);
                } catch (\Exception $e) {
                    Log::error("Notification Error (OrderPlaced): " . $e->getMessage());
                }

                return $order->id;
            });

            if ($paymentMethod === 'bakong') {
                return redirect()->route('orders.bakong-payment', $orderId);
            }

            if ($finalPaymentMethod === 'referral_full' || $remainingPayable <= 0) {
                $order = Order::find($orderId);
                if ($order) {
                    $order->update(['payment_status' => Order::PAYMENT_PAID, 'status' => 'completed']);
                }
                return redirect()->route('orders.show', $orderId)->with('success', 'Order fully covered by referral balance!');
            }

            // 💡 ពិនិត្យមើលថាតើ Fallback ជ្រើសរើស Cash on Delivery ដែរឬត់
            if (str_contains($finalPaymentMethod, 'cash_on_delivery')) {
                $order = Order::find($orderId);
                if ($order) {
                    $order->update([
                        'status' => Order::STATUS_PENDING,
                        'payment_status' => Order::PAYMENT_UNPAID
                    ]); 
                }
                return redirect()->route('orders.show', $orderId)->with('success', 'Order placed successfully! Remaining balance will be paid via Cash on Delivery.');
            }

            // ប្រសិនបើជា Bank Transfer ទើបវាធ្លាក់មកទំព័រ QR Code នេះ
            return redirect()->route('orders.payment', $orderId);

        } catch (\Exception $e) {
            Log::error("Place Order Error: " . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function bakongPayment($id)
    {
        $order = Order::where('user_id', Auth::id())->with('items.product')->findOrFail($id);
        
        $amount = number_format($order->total, 2, '.', ''); 
        $billNumber = $order->order_number;

        $account = '85560946598@bakong'; 
        $merchantName = 'Chamnab Sambath';
        $merchantCity = 'Phnom Penh';

        $tag30_sub  = "0016COM.BANKING.KHQR"; 
        $tag30_sub .= "01" . sprintf("%02d", strlen($account)) . $account; 
        $tag30 = "30" . sprintf("%02d", strlen($tag30_sub)) . $tag30_sub;

        $payload  = "000201"; 
        $payload .= "010212"; 
        $payload .= $tag30;   
        $payload .= "52040000"; 
        $payload .= "5303840";  
        $payload .= "54" . sprintf("%02d", strlen($amount)) . $amount; 
        $payload .= "5802KH";   
        
        $name = substr($merchantName, 0, 25);
        $payload .= "59" . sprintf("%02d", strlen($name)) . $name; 
        
        $city = substr($merchantCity, 0, 15);
        $payload .= "60" . sprintf("%02d", strlen($city)) . $city; 
        
        $tag62_sub  = "01" . sprintf("%02d", strlen($billNumber)) . $billNumber;
        $payload   .= "62" . sprintf("%02d", strlen($tag62_sub)) . $tag62_sub; 

        $payload .= "6304"; 
        $payload .= $this->calculateCRC($payload); 

        $qrSvg = QrCode::size(230)->generate($payload);

        return view('frontend.checkout.bakong', compact('order', 'qrSvg'));
    }

    private function calculateCRC($payload) {
        $polynomial = 0x1021;
        $result = 0xFFFF;
        
        for ($i = 0; $i < strlen($payload); $i++) {
            $result ^= (ord($payload[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                if (($result & 0x8000) != 0) {
                    $result = (($result << 1) ^ $polynomial);
                } else {
                    $result = ($result << 1);
                }
                $result &= 0xFFFF;
            }
        }
        return str_pad(strtoupper(dechex($result)), 4, '0', STR_PAD_LEFT);
    }

    public function payment($id) 
    { 
        $order = Order::where('user_id', Auth::id())->with('items')->findOrFail($id); 
        $bankInfo = $this->getBankInfo(); 
        return view('frontend.checkout.payment', compact('order', 'bankInfo')); 
    }

    public function process($id) 
    { 
        $order = Order::where('user_id', Auth::id())->with(['user', 'items'])->findOrFail($id); 
        $isCod = $order->payment_method === Order::METHOD_CASH_ON_DELIVERY || str_contains($order->payment_method, 'cash_on_delivery'); 
        
        if ($isCod && $order->status === Order::STATUS_PENDING) { 
            $order->update(['status' => Order::STATUS_CONFIRMED]); 
            try { 
                $this->loyalty->processOrderLoyalty($order); 
            } catch (\Exception $e) { 
                Log::error('Loyalty Process failed: ' . $e->getMessage()); 
            } 
        } 
        return redirect()->route('orders.show', $order->id)->with('success', 'Your order is confirmed!'); 
    }

    public function uploadReceipt(Request $request, $id)
    {
        $request->validate([
            'receipt' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('receipts', 'public');
            $order->receipt_image = $path;
            $order->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 400);
    }

    public function simulatePaid($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);
        $order->update([
            'payment_status' => Order::PAYMENT_PAID,
            'status' => 'completed'
        ]);

        return redirect()->route('orders.show', $order->id)->with('success', 'Payment simulated successfully!');
    }

    public function show($id) 
    { 
        $order = Order::where('user_id', Auth::id())->with('items.product')->findOrFail($id); 
        return view('frontend.orders.show', compact('order')); 
    }

    public function history() 
    { 
        $orders = Order::where('user_id', Auth::id())->with('items.product')->latest()->paginate(10); 
        return view('frontend.orders.history', compact('orders')); 
    }

    public function cancel($id) 
    { 
        $order = Order::where('user_id', Auth::id())->with('items.product')->findOrFail($id); 
        
        if (!$order->canBeCancelled()) { 
            return back()->with('error', 'This order cannot be cancelled.'); 
        } 
        
        DB::transaction(function () use ($order) { 
            foreach ($order->items as $item) { 
                if ($item->product) { 
                    $item->product->increment('stock', $item->quantity); 
                    $item->product->decrement('sold_count', $item->quantity); 
                } 
            } 
            $order->update([ 
                'status' => Order::STATUS_CANCELLED, 
                'canceled_at' => now(), 
            ]); 
        }); 
        return redirect()->route('orders.history')->with('success', 'Order has been cancelled.'); 
    }

    private function getBankInfo(): array 
    { 
        return [ 
            'bank_name' => Setting::where('key', 'bank_name')->value('value') ?? 'ACLEDA Bank', 
            'bank_account_name' => Setting::where('key', 'bank_account_name')->value('value') ?? 'SAMBATH CHAMNAB', 
            'bank_account_number' => Setting::where('key', 'bank_account_number')->value('value') ?? '000123456', 
            'bank_qr_usd' => Setting::where('key', 'bank_qr_usd')->value('value'), 
            'bank_qr_khr' => Setting::where('key', 'bank_qr_khr')->value('value'), 
        ]; 
    }
}