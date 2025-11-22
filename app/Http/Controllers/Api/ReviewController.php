<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Get session reviews
     */
    public function index(Request $request, $sessionId)
    {
        $query = Review::where('session_id', $sessionId)
            ->where('is_approved', true)
            ->with('user')
            ->orderBy('created_at', 'desc');

        $perPage = $request->get('per_page', 10);
        $reviews = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    /**
     * Get user reviews
     */
    public function myReviews(Request $request)
    {
        $reviews = $request->user()->reviews()
            ->with(['session', 'booking'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    /**
     * Create review
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $booking = Booking::find($request->booking_id);

        // Check if booking belongs to user
        if ($booking->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Check if booking can be reviewed
        if (!$booking->canBeReviewed()) {
            return response()->json([
                'success' => false,
                'message' => 'Booking cannot be reviewed'
            ], 400);
        }

        // Check if already reviewed
        if ($booking->review()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Booking already reviewed'
            ], 400);
        }

        // Create review
        $review = Review::create([
            'user_id' => $user->id,
            'session_id' => $booking->session_id,
            'booking_id' => $booking->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => true, // Auto-approve for now
        ]);

        // Update session rating
        $session = $booking->session;
        $session->updateRating();

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully',
            'data' => $review
        ], 201);
    }

    /**
     * Update review
     */
    public function update(Request $request, $id)
    {
        $review = $request->user()->reviews()->find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $review->update($request->only(['rating', 'comment']));

        // Update session rating
        $review->session->updateRating();

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'data' => $review
        ]);
    }

    /**
     * Delete review
     */
    public function destroy(Request $request, $id)
    {
        $review = $request->user()->reviews()->find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        $session = $review->session;
        $review->delete();

        // Update session rating
        $session->updateRating();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully'
        ]);
    }

    /**
     * Mark review as helpful
     */
    public function markHelpful($id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        $review->incrementHelpful();

        return response()->json([
            'success' => true,
            'message' => 'Review marked as helpful',
            'data' => $review
        ]);
    }
}
