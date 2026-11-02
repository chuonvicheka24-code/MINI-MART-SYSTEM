<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryStaff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'required|string|max:50',
            'vehicle' => 'required|string|max:100',
            'status' => 'nullable|in:available,delivery,off',
        ]);

        $staff = DeliveryStaff::create($data + ['status' => $data['status'] ?? 'available']);

        return response()->json(['staff' => $staff]);
    }

    public function update(Request $request, DeliveryStaff $staff): JsonResponse
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:50',
            'vehicle' => 'nullable|string|max:100',
            'status' => 'nullable|in:available,delivery,off',
        ]);

        $oldVehicle = $staff->vehicle;
        $staff->update(array_filter($data, fn ($v) => $v !== null && $v !== ''));

        if (isset($data['vehicle']) && $data['vehicle'] !== $oldVehicle) {
            // Keep orders already assigned to this staffer pointing at their new vehicle tag.
            $staff->orders()->update(['truck_number' => null]);
        }

        return response()->json(['staff' => $staff->fresh()]);
    }

    public function destroy(DeliveryStaff $staff): JsonResponse
    {
        $staff->delete();

        return response()->json(['ok' => true]);
    }
}