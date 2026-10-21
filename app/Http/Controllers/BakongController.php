<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use KHQR\BakongKHQR;
use KHQR\Models\IndividualInfo;
use KHQR\Helpers\KHQRData;

class BakongController extends Controller
{
    /**
     * Display the KHQR payment page for an order.
     */
    public function payment($id)
    {
        $order = Order::findOrFail($id);
        
        $bakongAccount = 'chamnab_sambath@bkrt'; // គណនីបាកុងរបស់បង
        $merchantName = "ShopStore";
        $amount = (float) $order->total; // ទឹកប្រាក់ Dynamic តាម Order (ឧ. 9.98)

        try {
            // កំណត់ព័ត៌មានសម្រាប់ Individual Account KHQR
            $individualInfo = new IndividualInfo(
                bakongAccountID: $bakongAccount,
                merchantName: $merchantName,
                merchantCity: 'PHNOM PENH',
                currency: KHQRData::CURRENCY_USD, // ប្រភេទលុយ USD
                amount: $amount, // បញ្ចូលទឹកប្រាក់ជា Dynamic ត្រង់នេះ
                billNumber: 'ORD-' . $order->id,
                storeLabel: 'ShopStore',
                terminalLabel: 'POS-01'
            );

            // ហៅ Library ដើម្បីបង្កើត KHQR String ផ្លូវការ (មានបញ្ចូល Amount រួចជាស្រេច)
            $response = BakongKHQR::generateIndividual($individualInfo);

            if ($response && isset($response->data['qr'])) {
                $qrCodeString = $response->data['qr'];
                
                // បង្កើត QR Code Image URL ពី String ដែលបាន generate ត្រឹមត្រូវតាមស្ដង់ដារ EMVCo
                $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($qrCodeString);

                return view('frontend.orders.payment', compact('order', 'qrImageUrl'));
            }
        } catch (\Exception $e) {
            // Log error បើមានបញ្ហា
        }

        // Fallback បរាជ័យ
        $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode("INVALID_QR");

        return view('frontend.orders.payment', compact('order', 'qrImageUrl'));
    }

    /**
     * Check transaction status from Bakong API.
     */
    public function checkStatus(Request $request)
    {
        $order = Order::find($request->order_id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'រកមិនឃើញការបញ្ជាទិញ']);
        }

        // ហៅ API បាគងដើម្បីទាញយកប្រវត្តិប្រតិបត្តិការ
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('BAKONG_TOKEN'),
        ])->get(env('BAKONG_API_URL') . "/get_transaction_history");

        if ($response->successful()) {
            $transactions = $response->json()['data'] ?? [];

            foreach ($transactions as $trx) {
                // ១. ឆែកចំនួនទឹកប្រាក់ (Amount)
                // ២. ឆែក Currency (USD)
                // ៣. ឆែកថាជាលុយចូល (Is Receiver)
                if (
                    floatval($trx['amount']) == floatval($order->total) &&
                    strtoupper($trx['currency']) == 'USD' &&
                    $trx['to_account_id'] == config('services.bakong.account_id')
                ) {
                    // បើរកឃើញប្រតិបត្តិការដែលត្រូវគ្នា
                    $order->update([
                        'status' => 'completed',
                        'payment_status' => Order::PAYMENT_PAID,
                        'transaction_hash' => $trx['hash'] ?? null
                    ]);

                    return response()->json(['success' => true]);
                }
            }
        }

        return response()->json(['success' => false]);
    }

    /**
     * Simulate successful payment for Demo purposes (ការពារពេលស្កែនមិនចេញ).
     */
    public function simulatePaid($id)
    {
        $order = Order::findOrFail($id);
        
        // ធ្វើការប្តូរ Status ទៅជា Paid និង Completed ភ្លាមៗ
        $order->update([
            'payment_status' => Order::PAYMENT_PAID,
            'status' => 'completed'
        ]);

        return redirect()->route('orders.show', $order->id)->with('success', 'Payment simulated successfully! Order is now paid.');
    }
}