<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\PayWayService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    protected $payWayService;

    public function __construct(PayWayService $payWayService)
    {
        $this->payWayService = $payWayService;
    }

    /**
     * Show cart + generate ABA checkout data
     */
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('products.index')
                ->with('error', 'Your cart is empty');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $merchant_id    = 'ec463509';
        $req_time       = time();
        $tranId         = 'ORD-' . $req_time;
        $amount         = number_format($total, 2, '.', '');
        $currency       = 'USD';
        $payment_option = 'abapay_khqr';

        // ✅ FIXED: redirect back to checkTransaction
        $continue_success_url = route('payment.check');

        // ✅ Save tran_id in session
        session(['tran_id' => $tranId]);

        $hash = $this->payWayService->getHash(
            $req_time .
                $merchant_id .
                $tranId .
                $amount .
                $payment_option .
                $continue_success_url .
                $currency
        );

        return view('cart.index', compact(
            'cart',
            'total',
            'hash',
            'tranId',
            'amount',
            'payment_option',
            'merchant_id',
            'req_time',
            'continue_success_url',
            'currency'
        ));
    }


    /**
     * Add product to cart
     */
    public function add($id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'name'     => $product->name,
                'price'    => $product->price,
                'quantity' => 1,
                'image'    => $product->image
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Product added to cart');
    }

    /**
     * Remove item from cart
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        if (empty($cart)) {
            return redirect()->route('products.index');
        }

        return back();
    }

    /**
     * User return URL – manual check after ABA redirect
     */
    public function checkTransaction(Request $request)
    {
        Log::info('CHECK TRANSACTION HIT', [
            'query'        => $request->query(),
            'session_tran' => session('tran_id'),
            'full_url'     => $request->fullUrl(),
        ]);

        // ✅ Try request first, fallback to session
        $tranId = $request->query('tran_id') ?? session('tran_id');

        if (!$tranId) {
            return redirect()->route('cart.index')
                ->with('error', 'Transaction ID missing');
        }

        if ($this->checkAbaApproved($tranId)) {

            // ✅ CLEAR CART CORRECTLY
            session()->forget('cart');
            session()->forget('tran_id');

            Log::info('PAYMENT CONFIRMED & CART CLEARED', [
                'tran_id' => $tranId
            ]);

            return redirect()->route('products.index')
                ->with('success', 'Payment successful. Thank you!');
        }

        return redirect()->route('cart.index')
            ->with('error', 'Payment not completed yet. Please try again.');
    }


    /**
     * ABA Pushback (IPN)
     */
    public function pushback(Request $request)
    {
        Log::info('ABA Pushback received', $request->all());

        $validated = $request->validate([
            'tran_id' => 'required|string',
            'status'  => 'required|string',
            'hash'    => 'required|string',
        ]);

        // Simple hash validation
        $expectedHash = $this->payWayService->getHash(
            $validated['tran_id'] . $validated['status']
        );

        if ($validated['hash'] !== $expectedHash) {
            Log::warning('Invalid ABA pushback hash', [
                'tran_id' => $validated['tran_id'],
            ]);

            return response()->json(['message' => 'Invalid hash'], 400);
        }

        // Double check with ABA API
        if ($this->checkAbaApproved($validated['tran_id'])) {
            session()->forget('cart');

            Log::info('ABA pushback approved', [
                'tran_id' => $validated['tran_id'],
            ]);

            return response()->json(['message' => 'Payment approved']);
        }

        return response()->json(['message' => 'Payment not approved'], 400);
    }

    public function logTranId(Request $request)
    {
        Log::info('LOG TRAN ID FROM REQUEST', [
            'all_query' => $request->query(),
            'tran_id'   => $request->query('tran_id'),
            'full_url'  => $request->fullUrl(),
        ]);

        return response()->json([
            'message' => 'tran_id logged',
            'tran_id' => $request->query('tran_id'),
        ]);
    }

    public function logTranIdFromSession()
    {
        Log::info('LOG TRAN ID FROM SESSION', [
            'session_tran_id' => session('tran_id'),
            'all_session'     => session()->all(),
        ]);

        return response()->json([
            'message' => 'session tran_id logged',
            'tran_id' => session('tran_id'),
        ]);
    }


    /**
     * ABA verification helper
     */
    private function checkAbaApproved(string $tranId): bool
    {
        $merchantId = 'ec463509';
        $publicKey  = '1cb54f9442ec911e271b1774a995d39ecbfb28cc';
        $reqTime    = time();

        $hash = base64_encode(hash_hmac(
            'sha512',
            $reqTime . $merchantId . $tranId,
            $publicKey,
            true
        ));

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'User-Agent'   => 'CartDemo/1.0 (Laravel)',
        ])->post(
            'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/check-transaction-2',
            [
                'req_time'    => $reqTime,
                'merchant_id' => $merchantId,
                'tran_id'     => $tranId,
                'hash'        => $hash,
            ]
        );

        if (!$response->successful()) {
            Log::warning('ABA check failed', [
                'tran_id' => $tranId,
                'body'    => $response->body(),
            ]);
            return false;
        }

        $data = $response->json()['data'] ?? null;
        if (!$data) return false;

        $paymentStatus = $data['payment_status'] ?? null;
        $status        = $data['status'] ?? null;

        return ($paymentStatus === 'APPROVED') || ($status === 'success');
    }
}
