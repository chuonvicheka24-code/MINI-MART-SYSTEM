<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    /**
     * Send an HTML-formatted message to the store's Telegram chat.
     * Never throws — a Telegram outage must not break checkout or contact forms.
     */
    public static function send(string $text): bool
    {
        $token  = config('services.telegram.token');
        $chatId = config('services.telegram.chat_id');

        if (! $token || ! $chatId) {
            return false; // not configured
        }

        try {
            $response = Http::timeout(5)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id'    => $chatId,
                'text'       => $text,
                'parse_mode' => 'HTML',
            ]);

            if (! $response->successful()) {
                Log::warning('Telegram send failed: ' . $response->body());
            }

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('Telegram error: ' . $e->getMessage());

            return false;
        }
    }

    /** Escape user text for Telegram HTML parse mode. */
    public static function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    /** Step 2 ("out") / Step 3 ("done") order message. */
    public static function orderUpdate(Order $order, string $step): bool
    {
        $order->loadMissing('deliveryStaff');

        $head = $step === 'out'
            ? "🚚 <b>Order #{$order->id} is OUT FOR DELIVERY</b>"
            : "✅ <b>Order #{$order->id} was DELIVERED</b>";

        $text = $head . "\n"
            . "👤 " . self::e($order->customer_name) . "\n"
            . "📞 " . self::e($order->customer_phone) . "\n"
            . "📍 " . self::e($order->address) . "\n"
            . "🚛 " . self::e(ucfirst((string) $order->transport_type)) . ' ' . self::e($order->truck_number) . "\n";

        if ($order->deliveryStaff) {
            $text .= "🧑‍✈️ Driver: " . self::e($order->deliveryStaff->name) . "\n";
        }

        $text .= "💰 Total: $" . number_format($order->total, 2);

        return self::send($text);
    }
}