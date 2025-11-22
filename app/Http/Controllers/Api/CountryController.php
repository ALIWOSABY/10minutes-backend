<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Get all active countries
     */
    public function index()
    {
        $countries = Country::where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $countries
        ]);
    }

    /**
     * Get single country
     */
    public function show($id)
    {
        $country = Country::find($id);

        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => 'Country not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $country
        ]);
    }
}
