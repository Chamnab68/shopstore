<?php

namespace App\Http\Controllers;

use App\Models\{Order, OrderItem, Referral, Product};
use App\Services\{TelegramService, NotificationService, LoyaltyService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Log};

class CheckoutController extends Controller
{
    public function __construct(
        private TelegramService $telegram,
        private NotificationService $notification,
        private LoyaltyService $loyalty 
    ) {}

    // ── Checkout Page ─────────────────────────────────────────────────────────
    public function index()
    {
        $user = auth()->user();
        $cart = session()->get('cart', []);

        $cartItems = collect();
        $subtotal = 0;

        foreach ($cart as $id => $details) {
            $product = Product::find($id);
            if ($product) {
                $cartItems->put($id, (object)[
                    'product' => $product,
                    'product_id' => $id,
                    'quantity' => $details['quantity'],
                    'price' => $details['price']
                ]);
                $subtotal += $details['price'] * $details['quantity'];
            }
        }

        $shippingFee = $subtotal > 50 ? 0 : 0.99;
        $total = $subtotal + $shippingFee;

        // គណនា Referral Balance របស់ User
        $convertedCount = $user->referralsMade()->where('status', Referral::STATUS_CONVERTED)->count();
        $rewardPerUser = 0.10; 
        $totalEarned = $convertedCount * $rewardPerUser;
        $totalSpent = Order::where('user_id', $user->id)->sum('referral_amount_used');
        
        $referralBalance = max(0, $totalEarned - $totalSpent);

        return view('frontend.checkout.index', compact(
            'cartItems', 
            'subtotal', 
            'shippingFee', 
            'total', 
            'referralBalance'
        ));
    }

    public function placeOrder(Request $request)
{
    $user = auth()->user();
    
    // គណនាតម្លៃសរុបពិតប្រាកដពី Cart របស់អ្នក (ឧទាហរណ៍ទាញពី Session ឬ Cart items)
    // កែសម្រួលត្រង់នេះដោយថែម ->get() ពីមុខ sum()
$cartTotal = \App\Models\Cart::where('user_id', $user->id)->get()->sum(function($item) {
    return $item->price * $item->quantity;
}); 
    // ឬប្រសិនបើប្រព័ន្ធបងប្រើប្រាស់ Cart Facade ផ្សេង អាចទាញតម្លៃ Total មកដាក់ជំនួសទីនេះបាន

    $request->validate([
        'payment_method' => 'required|string',
    ]);

    $paymentMethod = $request->payment_method;
    $referralBalance = $user->referral_balance ?? 0; 

    $amountPaidFromReferral = 0;
    $amountToPayExtra = 0;
    $paymentStatus = 'pending';

    if ($paymentMethod === 'referral_balance') {
        if ($referralBalance >= $cartTotal) {
            // ករណីទី ១៖ Balance គ្រប់ ឬលើស
            $amountPaidFromReferral = $cartTotal;
            $amountToPayExtra = 0;
            $paymentStatus = 'paid'; 

            // កាត់កងទឹកប្រាក់ Referral របស់ User ចេញ
            $user->decrement('referral_balance', $cartTotal);

        } else {
            // ករណីទី ២៖ Balance មិនគ្រាន់ (Partial Payment)
            $amountPaidFromReferral = $referralBalance; 
            $amountToPayExtra = $cartTotal - $referralBalance; 
            
            // កាត់កង Referral Balance ឱ្យសល់ 0
            $user->update(['referral_balance' => 0]);

            // ⚠️ បើទឹកប្រាក់ខ្វះ ($amountToPayExtra) បងអាច Redirect គាត់ឱ្យទៅបង់លុយបន្ថែមតាម Bakong KHQR បន្ត
        }
    }

    // បង្កើត Order ក្នុង Database
    $order = Order::create([
        'user_id' => $user->id,
        'total' => $cartTotal,
        'referral_amount_used' => $amountPaidFromReferral,
        'extra_amount_to_pay' => $amountToPayExtra,
        'payment_method' => $paymentMethod,
        'payment_status' => $paymentStatus,
        'status' => 'processing',
    ]);

    // លុบทิ้งទំនិញក្នុង Cart ក្រោយពេលដាក់ Order สำเร็จ
    \App\Models\Cart::where('user_id', $user->id)->delete();

    return redirect()->route('orders.show', $order->id)->with('success', 'Order placed successfully!');
}

public function index() // ឬ checkout() អាស្រ័យលើ function របស់បង
{
    $user = auth()->user();
    
    // ទាញយកទឹកប្រាក់ Referral Balance របស់ User មកជាមួយ
    $referralBalance = $user->referral_balance ?? 0; 

    // ទាញយកទំនិញក្នុង Cart...
    
    return view('frontend.checkout', compact('referralBalance')); 
}

    // ── Store / Place Order ──────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'shipping_name'             => 'required|string|max:100',
            'shipping_phone'            => 'required|string|max:20',
            'shipping_address'          => 'required|string|max:500',
            'notes'                     => 'nullable|string|max:500',
            'payment_method'            => 'required|in:bank_transfer,cash_on_delivery,referral',
            'fallback_payment_method'   => 'nullable|in:bank_transfer,cash_on_delivery',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // ឆែកមើល Stock ឡើងវិញមុនពេលបញ្ជាទិញ
        foreach ($cart as $id => $details) {
            $product = Product::find($id);
            if (!$product || $product->stock < $details['quantity']) {
                return back()->with('error', "Sorry, " . ($details['name'] ?? 'Product') . " is out of stock.");
            }
        }

        $subtotal = 0;
        foreach ($cart as $details) {
            $subtotal += $details['price'] * $details['quantity'];
        }

        $shippingFee = $subtotal > 50 ? 0 : 0.99; 
        $total = $subtotal + $shippingFee;

        $user = Auth::user();
        $convertedCount = $user->referralsMade()->where('status', Referral::STATUS_CONVERTED)->count();
        $rewardPerUser = 0.10; 
        $totalEarned = $convertedCount * $rewardPerUser;
        $totalSpent = Order::where('user_id', $user->id)->sum('referral_amount_used');
        $currentReferralBalance = max(0, $totalEarned - $totalSpent);

        $paymentMethod = $request->payment_method;
        $referralAmountUsed = 0;
        $finalPaymentMethod = $paymentMethod;

        if ($paymentMethod === 'referral') {
            if ($currentReferralBalance >= $total) {
                $referralAmountUsed = $total;
                $finalPaymentMethod = 'referral_full';
            } else {
                $referralAmountUsed = $currentReferralBalance;
                $fallback = $request->input('fallback_payment_method', 'bank_transfer');
                $finalPaymentMethod = 'referral_mixed_' . $fallback;
            }
        }

        $remainingPayable = $total - $referralAmountUsed;

        try {
            $orderId = DB::transaction(function () use ($request, $cart, $subtotal, $shippingFee, $total, $referralAmountUsed, $remainingPayable, $finalPaymentMethod) {

                $order = Order::create([
                    'order_number'         => Order::generateOrderNumber(),
                    'user_id'              => Auth::id(),
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

                // សម្អាត Session Cart បន្ទាប់ពីបញ្ជាទិញរួច
                session()->forget('cart');

                try {
                    $this->notification->orderPlaced($order);
                } catch (\Exception $e) {
                    Log::error("Notification Error (OrderPlaced): " . $e->getMessage());
                }

                return $order->id;
            });

            if ($finalPaymentMethod === 'referral_full' || $remainingPayable <= 0) {
                $order = Order::find($orderId);
                if ($order) {
                    $order->update(['payment_status' => Order::PAYMENT_PAID, 'status' => 'completed']);
                }
                return redirect()->route('orders.show', $orderId)->with('success', 'Order fully covered by referral balance!');
            }

            return redirect()->route('orders.payment', $orderId);

        } catch (\Exception $e) {
            Log::error("Place Order Error: " . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }
}