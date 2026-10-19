<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryStaff;
use App\Models\Order;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function approve(Order $order): JsonResponse
    {
        $order->update(['status' => 'out']);

        $this->notify($order, 'out');

        return response()->json(['order' => $order->fresh(['items', 'deliveryStaff'])]);
    }

    public function reject(Order $order): JsonResponse
    {
        $order->update(['status' => 'rejected']);

        return response()->json(['order' => $order->fresh(['items', 'deliveryStaff'])]);
    }

    public function deliver(Order $order): JsonResponse
    {
        $order->update(['status' => 'done']);

        $this->notify($order, 'done');

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

    /**
     * Send a Telegram message when an order reaches step 2 (out) or step 3 (done).
     */
    private function notify(Order $order, string $step): void
    {
        $order->loadMissing('deliveryStaff');
        $e = fn ($v) => TelegramService::e($v);

        $head = $step === 'out'
            ? "🚚 <b>Order #{$order->id} is OUT FOR DELIVERY</b>"
            : "✅ <b>Order #{$order->id} was DELIVERED</b>";

        $text = $head . "\n"
            . "👤 {$e($order->customer_name)}\n"
            . "📞 {$e($order->customer_phone)}\n"
            . "📍 {$e($order->address)}\n"
            . "🚛 {$e(ucfirst($order->transport_type))} {$e($order->truck_number)}\n";

        if ($order->deliveryStaff) {
            $text .= "🧑‍✈️ Driver: {$e($order->deliveryStaff->name)}\n";
        }

        $text .= "💰 Total: $" . number_format($order->total, 2);

        TelegramService::send($text);
    }
}