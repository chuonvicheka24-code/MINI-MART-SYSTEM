<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'store_name' => 'nullable|string|max:150',
            'hours' => 'nullable|string|max:150',
            'delivery_rate' => 'nullable|numeric|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
        ]);

        $settings = Setting::current();
        $settings->update(array_filter($data, fn ($v) => $v !== null && $v !== ''));

        return response()->json(['settings' => $settings->fresh()]);
    }
}