<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * Get user bookings
     */
    public function index(Request $request)
    {
        $query = $request->user()->bookings()
            ->with(['session.trainer', 'session.category']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 10);
        $bookings = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }

    /**
     * Get single booking
     */
    public function show(Request $request, $id)
    {
        $booking = $request->user()->bookings()
            ->with(['session.trainer', 'session.category', 'review'])
            ->find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $booking
        ]);
    }

    /**
     * Create new booking
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|exists:training_sessions,id',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $session = Session::find($request->session_id);

        if (!$session || !$session->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Session not available'
            ], 404);
        }

        $user = $request->user();

        // Check if user has enough credits
        if (!$user->hasEnoughCredits($session->price_coins)) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient credits',
                'data' => [
                    'required' => $session->price_coins,
                    'available' => $user->credit_balance,
                    'needed' => $session->price_coins - $user->credit_balance
                ]
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Create booking
            $booking = Booking::create([
                'user_id' => $user->id,
                'session_id' => $session->id,
                'price_paid' => $session->price_coins,
                'status' => 'confirmed',
                'scheduled_at' => $request->scheduled_at ?? now(),
            ]);

            // Deduct credits
            $balanceBefore = $user->credit_balance;
            $user->decrement('credit_balance', $session->price_coins);
            $user->refresh();

            // Create transaction record
            $transaction = $user->transactions()->create([
                'type' => 'debit',
                'amount' => $session->price_coins,
                'balance_before' => $balanceBefore,
                'balance_after' => $user->credit_balance,
                'source' => 'booking',
                'reference_type' => Booking::class,
                'reference_id' => $booking->id,
                'description' => "Booked session: {$session->title}",
            ]);

            // Update session bookings count
            $session->increment('bookings_count');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully',
                'data' => [
                    'booking' => $booking->load('session.trainer'),
                    'remaining_balance' => $user->credit_balance
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Booking failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Cancel booking
     */
    public function cancel(Request $request, $id)
    {
        $booking = $request->user()->bookings()->find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        if (!$booking->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Booking cannot be cancelled'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $user = $request->user();
            $refundAmount = $booking->price_paid;

            // Update booking status
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->reason,
            ]);

            // Refund credits
            $balanceBefore = $user->credit_balance;
            $user->increment('credit_balance', $refundAmount);
            $user->refresh();

            // Create refund transaction
            $user->transactions()->create([
                'type' => 'credit',
                'amount' => $refundAmount,
                'balance_before' => $balanceBefore,
                'balance_after' => $user->credit_balance,
                'source' => 'refund',
                'reference_type' => Booking::class,
                'reference_id' => $booking->id,
                'description' => "Refund for cancelled booking",
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully',
                'data' => [
                    'booking' => $booking,
                    'refunded_amount' => $refundAmount,
                    'new_balance' => $user->credit_balance
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Cancellation failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Start session
     */
    public function startSession(Request $request, $id)
    {
        $booking = $request->user()->bookings()->find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        if ($booking->status !== 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => 'Booking is not confirmed'
            ], 400);
        }

        $booking->update([
            'status' => 'completed',
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Session started',
            'data' => $booking->load('session.trainer')
        ]);
    }
}
