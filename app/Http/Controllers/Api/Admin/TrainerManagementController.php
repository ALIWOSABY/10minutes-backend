<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrainerManagementController extends Controller
{
    /**
     * Get all trainers
     */
    public function index(Request $request)
    {
        $query = Trainer::query();

        // Filter by platform type
        if ($request->has('platform_type')) {
            $query->where('platform_type', $request->platform_type);
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $trainers = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $trainers
        ]);
    }

    /**
     * Get single trainer
     */
    public function show($id)
    {
        $trainer = Trainer::with('sessions')->find($id);

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
     * Create trainer
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|unique:trainers',
            'bio' => 'nullable|string',
            'full_bio' => 'nullable|string',
            'avatar' => 'nullable|string',
            'specialization' => 'nullable|string|max:100',
            'skills' => 'nullable|array',
            'languages' => 'nullable|array',
            'education' => 'nullable|array',
            'experience' => 'nullable|array',
            'certifications' => 'nullable|array',
            'platform_type' => 'required|in:training,consultation,both',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $trainer = Trainer::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Trainer created successfully',
            'data' => $trainer
        ], 201);
    }

    /**
     * Update trainer
     */
    public function update(Request $request, $id)
    {
        $trainer = Trainer::find($id);

        if (!$trainer) {
            return response()->json([
                'success' => false,
                'message' => 'Trainer not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:100',
            'slug' => 'nullable|string|unique:trainers,slug,' . $id,
            'bio' => 'nullable|string',
            'full_bio' => 'nullable|string',
            'avatar' => 'nullable|string',
            'specialization' => 'nullable|string|max:100',
            'skills' => 'nullable|array',
            'languages' => 'nullable|array',
            'education' => 'nullable|array',
            'experience' => 'nullable|array',
            'certifications' => 'nullable|array',
            'platform_type' => 'nullable|in:training,consultation,both',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $trainer->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Trainer updated successfully',
            'data' => $trainer
        ]);
    }

    /**
     * Delete trainer
     */
    public function destroy($id)
    {
        $trainer = Trainer::find($id);

        if (!$trainer) {
            return response()->json([
                'success' => false,
                'message' => 'Trainer not found'
            ], 404);
        }

        $trainer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Trainer deleted successfully'
        ]);
    }

    /**
     * Toggle trainer status
     */
    public function toggleStatus($id)
    {
        $trainer = Trainer::find($id);

        if (!$trainer) {
            return response()->json([
                'success' => false,
                'message' => 'Trainer not found'
            ], 404);
        }

        $trainer->update(['is_active' => !$trainer->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Trainer status updated successfully',
            'data' => $trainer
        ]);
    }
}
