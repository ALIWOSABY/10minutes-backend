<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CreditController extends Controller
{
    /**
     * Get user credit balance
     */
    public function balance(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'balance' => $user->credit_balance,
                'currency' => $user->country->currency_code ?? 'SAR',
            ]
        ]);
    }

    /**
     * Get credit packages
     */
    public function packages()
    {
        $coinsPerDollar = setting('coins_per_dollar', 2);

        $packages = [
            [
                'id' => 1,
                'name' => 'Basic Package',
                'coins' => 10,
                'price' => 5,
                'currency' => 'SAR',
                'discount' => 0,
                'popular' => false,
            ],
            [
                'id' => 2,
                'name' => 'Silver Package',
                'coins' => 25,
                'price' => 10,
                'currency' => 'SAR',
                'discount' => 10,
                'original_price' => 12.5,
                'popular' => false,
            ],
            [
                'id' => 3,
                'name' => 'Gold Package',
                'coins' => 50,
                'price' => 18,
                'currency' => 'SAR',
                'discount' => 20,
                'original_price' => 25,
                'popular' => true,
            ],
            [
                'id' => 4,
                'name' => 'Platinum Package',
                'coins' => 100,
                'price' => 30,
                'currency' => 'SAR',
                'discount' => 30,
                'original_price' => 50,
                'popular' => false,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $packages
        ]);
    }

    /**
     * Purchase credits (Placeholder - will integrate HyperPay later)
     */
    public function purchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'required|integer|min:1|max:4',
            'payment_method' => 'required|in:credit_card,paypal,apple_pay,google_pay',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Package details
        $packages = [
            1 => ['coins' => 10, 'price' => 5],
            2 => ['coins' => 25, 'price' => 10],
            3 => ['coins' => 50, 'price' => 18],
            4 => ['coins' => 100, 'price' => 30],
        ];

        $package = $packages[$request->package_id] ?? null;

        if (!$package) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid package'
            ], 400);
        }

        $user = $request->user();

        DB::beginTransaction();
        try {
            // Create payment record (pending)
            $payment = Payment::create([
                'user_id' => $user->id,
                'amount' => $package['price'],
                'currency' => 'SAR',
                'coins_purchased' => $package['coins'],
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'gateway' => 'hyperpay', // Placeholder
            ]);

            // TODO: Integrate with HyperPay gateway here
            // For now, we'll simulate successful payment

            // Simulate successful payment
            $payment->update([
                'status' => 'completed',
                'completed_at' => now(),
                'gateway_transaction_id' => 'DEMO_' . time(),
            ]);

            // Add credits to user
            $balanceBefore = $user->credit_balance;
            $user->increment('credit_balance', $package['coins']);
            $user->refresh();

            // Create transaction record
            $user->transactions()->create([
                'type' => 'credit',
                'amount' => $package['coins'],
                'balance_before' => $balanceBefore,
                'balance_after' => $user->credit_balance,
                'source' => 'purchase',
                'reference_type' => Payment::class,
                'reference_id' => $payment->id,
                'description' => "Purchased {$package['coins']} coins",
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Credits purchased successfully',
                'data' => [
                    'payment' => $payment,
                    'coins_added' => $package['coins'],
                    'new_balance' => $user->credit_balance
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Payment failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Get transaction history
     */
    public function transactions(Request $request)
    {
        $query = $request->user()->transactions()
            ->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by source
        if ($request->has('source')) {
            $query->where('source', $request->source);
        }

        $perPage = $request->get('per_page', 20);
        $transactions = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    /**
     * Get payment history
     */
    public function payments(Request $request)
    {
        $query = $request->user()->payments()
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 20);
        $payments = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }
}
