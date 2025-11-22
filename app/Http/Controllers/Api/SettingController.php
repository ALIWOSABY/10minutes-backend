<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Get all public settings
     */
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Get specific setting
     */
    public function show($key)
    {
        $value = Setting::get($key);

        if ($value === null) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'key' => $key,
                'value' => $value
            ]
        ]);
    }
}
