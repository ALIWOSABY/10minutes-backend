<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SessionManagementController extends Controller
{
    /**
     * Get all sessions
     */
    public function index(Request $request)
    {
        $query = Session::with(['category', 'trainer']);

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

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $sessions = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }

    /**
     * Create session
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'trainer_id' => 'required|exists:trainers,id',
            'platform_type' => 'required|in:training,consultation',
            'price_coins' => 'required|numeric|min:0',
            'level' => 'required|in:beginner,intermediate,advanced,all',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $session = Session::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Session created successfully',
            'data' => $session
        ], 201);
    }

    /**
     * Update session
     */
    public function update(Request $request, $id)
    {
        $session = Session::find($id);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:200',
            'description' => 'sometimes|string',
            'category_id' => 'sometimes|exists:categories,id',
            'trainer_id' => 'sometimes|exists:trainers,id',
            'platform_type' => 'sometimes|in:training,consultation',
            'price_coins' => 'sometimes|numeric|min:0',
            'level' => 'sometimes|in:beginner,intermediate,advanced,all',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $session->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $session
        ]);
    }

    /**
     * Delete session
     */
    public function destroy($id)
    {
        $session = Session::find($id);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found'
            ], 404);
        }

        $session->delete();

        return response()->json([
            'success' => true,
            'message' => 'Session deleted successfully'
        ]);
    }

    /**
     * Toggle status
     */
    public function toggleStatus($id)
    {
        $session = Session::find($id);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found'
            ], 404);
        }

        $session->update(['is_active' => !$session->is_active]);

        return response()->json([
            'success' => true,
            'data' => $session
        ]);
    }
}
