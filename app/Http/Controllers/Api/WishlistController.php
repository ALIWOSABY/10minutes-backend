<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Get user wishlist
     */
    public function index(Request $request)
    {
        $wishlist = $request->user()->wishlist()
            ->with(['session.trainer', 'session.category'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $wishlist
        ]);
    }

    /**
     * Add to wishlist
     */
    public function store(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:training_sessions,id'
        ]);

        $user = $request->user();

        // Check if already in wishlist
        $exists = $user->wishlist()
            ->where('session_id', $request->session_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Session already in wishlist'
            ], 400);
        }

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'session_id' => $request->session_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Added to wishlist',
            'data' => $wishlist->load('session')
        ], 201);
    }

    /**
     * Remove from wishlist
     */
    public function destroy(Request $request, $id)
    {
        $wishlist = $request->user()->wishlist()->find($id);

        if (!$wishlist) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in wishlist'
            ], 404);
        }

        $wishlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Removed from wishlist'
        ]);
    }

    /**
     * Check if session is in wishlist
     */
    public function check(Request $request, $sessionId)
    {
        $exists = $request->user()->wishlist()
            ->where('session_id', $sessionId)
            ->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'in_wishlist' => $exists
            ]
        ]);
    }
}
