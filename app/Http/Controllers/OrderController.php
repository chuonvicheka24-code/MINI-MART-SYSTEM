<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryStaff;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function approve(Order $order): JsonResponse
    {
        $order->update(['status' => 'out']);

        return response()->json(['order' => $order->fresh(['items', 'deliveryStaff'])]);
    }

    public function deliver(Order $order): JsonResponse
    {
        $order->update(['status' => 'done']);

        return response()->json(['order' => $order->fresh(['items', 'deliveryStaff'])]);
    }

    public function assign(Request $request, Order $order): JsonResponse
    {
        $data = $request->validate(['staff_id' => 'nullable|exists:delivery_staff,id']);

        $order->update(['delivery_staff_id' => $data['staff_id'] ?? null]);

        if ($data['staff_id'] ?? null) {
            $staff = DeliveryStaff::find($data['staff_id']);
            $order->update([
                'transport_type' => str_contains(strtolower($staff->vehicle), 'moto') ? 'moto' : 'truck',
            ]);
        }

        return response()->json(['order' => $order->fresh(['items', 'deliveryStaff'])]);
    }
}