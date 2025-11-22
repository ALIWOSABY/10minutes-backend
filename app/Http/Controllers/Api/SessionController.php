<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    /**
     * Get all sessions
     */
    public function index(Request $request)
    {
        $query = Session::where('is_active', true)
            ->with(['category', 'trainer']);

        // Filter by platform type
        if ($request->has('platform_type')) {
            $query->where('platform_type', $request->platform_type);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by trainer
        if ($request->has('trainer_id')) {
            $query->where('trainer_id', $request->trainer_id);
        }

        // Filter by level
        if ($request->has('level')) {
            $query->where(function($q) use ($request) {
                $q->where('level', $request->level)
                  ->orWhere('level', 'all');
            });
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price_coins', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price_coins', '<=', $request->max_price);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'popular') {
            $query->orderBy('bookings_count', 'desc');
        } elseif ($sortBy === 'rating') {
            $query->orderBy('rating', 'desc');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = $request->get('per_page', 12);
        $sessions = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }

    /**
     * Get featured sessions
     */
    public function featured(Request $request)
    {
        $platformType = $request->get('platform_type');

        $query = Session::where('is_active', true)
            ->where('is_featured', true)
            ->with(['category', 'trainer']);

        if ($platformType) {
            $query->where('platform_type', $platformType);
        }

        $sessions = $query->orderBy('rating', 'desc')
            ->limit(6)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }

    /**
     * Get most viewed sessions
     */
    public function mostViewed(Request $request)
    {
        $platformType = $request->get('platform_type');

        $query = Session::where('is_active', true)
            ->with(['category', 'trainer']);

        if ($platformType) {
            $query->where('platform_type', $platformType);
        }

        $sessions = $query->orderBy('views_count', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }

    /**
     * Get latest sessions
     */
    public function latest(Request $request)
    {
        $platformType = $request->get('platform_type');

        $query = Session::where('is_active', true)
            ->with(['category', 'trainer']);

        if ($platformType) {
            $query->where('platform_type', $platformType);
        }

        $sessions = $query->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }

    /**
     * Get single session
     */
    public function show($slug)
    {
        $session = Session::where('slug', $slug)
            ->where('is_active', true)
            ->with(['category', 'trainer', 'reviews' => function($query) {
                $query->where('is_approved', true)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->with('user');
            }])
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found'
            ], 404);
        }

        // Increment views
        $session->incrementViews();

        return response()->json([
            'success' => true,
            'data' => $session
        ]);
    }

    /**
     * Get related sessions
     */
    public function related($slug)
    {
        $session = Session::where('slug', $slug)->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found'
            ], 404);
        }

        $related = Session::where('is_active', true)
            ->where('id', '!=', $session->id)
            ->where(function($query) use ($session) {
                $query->where('category_id', $session->category_id)
                    ->orWhere('trainer_id', $session->trainer_id);
            })
            ->with(['category', 'trainer'])
            ->orderBy('rating', 'desc')
            ->limit(6)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $related
        ]);
    }
}
