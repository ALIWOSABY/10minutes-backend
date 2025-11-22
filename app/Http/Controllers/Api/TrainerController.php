<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use Illuminate\Http\Request;

class TrainerController extends Controller
{
    /**
     * Get all trainers
     */
    public function index(Request $request)
    {
        $query = Trainer::where('is_active', true);

        // Filter by platform type
        if ($request->has('platform_type')) {
            $query->where(function($q) use ($request) {
                $q->where('platform_type', $request->platform_type)
                  ->orWhere('platform_type', 'both');
            });
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%")
                  ->orWhere('bio', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'rating');
        $sortOrder = $request->get('sort_order', 'desc');

        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 12);
        $trainers = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $trainers
        ]);
    }

    /**
     * Get single trainer
     */
    public function show($slug)
    {
        $trainer = Trainer::where('slug', $slug)
            ->where('is_active', true)
            ->with(['sessions' => function($query) {
                $query->where('is_active', true)
                    ->orderBy('bookings_count', 'desc')
                    ->limit(6);
            }])
            ->first();

        if (!$trainer) {
            return response()->json([
                'success' => false,
                'message' => 'Trainer not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $trainer
        ]);
    }

    /**
     * Get trainer sessions
     */
    public function sessions($slug, Request $request)
    {
        $trainer = Trainer::where('slug', $slug)->first();

        if (!$trainer) {
            return response()->json([
                'success' => false,
                'message' => 'Trainer not found'
            ], 404);
        }

        $query = $trainer->sessions()
            ->where('is_active', true)
            ->with(['category', 'trainer']);

        $perPage = $request->get('per_page', 12);
        $sessions = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }
}
