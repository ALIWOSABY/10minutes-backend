<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get all categories
     */
    public function index(Request $request)
    {
        $query = Category::where('is_active', true)
            ->with('children')
            ->whereNull('parent_id');

        // Filter by platform type
        if ($request->has('platform_type')) {
            $query->where(function($q) use ($request) {
                $q->where('platform_type', $request->platform_type)
                  ->orWhere('platform_type', 'both');
            });
        }

        $categories = $query->orderBy('order')->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get single category with children
     */
    public function show($id)
    {
        $category = Category::with(['children', 'parent'])
            ->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Get category tree (all levels)
     */
    public function tree(Request $request)
    {
        $query = Category::where('is_active', true)
            ->with('children.children')
            ->whereNull('parent_id');

        if ($request->has('platform_type')) {
            $query->where(function($q) use ($request) {
                $q->where('platform_type', $request->platform_type)
                  ->orWhere('platform_type', 'both');
            });
        }

        $categories = $query->orderBy('order')->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}
