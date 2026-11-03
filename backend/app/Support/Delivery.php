<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Delivery rules (single source of truth; the checkout page mirrors them in JS).
 *
 *  - Motorcycle only if the basket is <= 10 kg AND <= 24 counted units. Otherwise truck only.
 *  - Fee = flat base price (Setting: moto $2 / truck $3) for the first 10 km,
 *    then $0.25 for every extra km.
 */
class Delivery
{
    public const FREE_KM = 10;        // distance covered by the flat base price
    public const PER_KM = 0.25;       // $ per km beyond FREE_KM
    public const MOTO_MAX_KG = 10;
    public const MOTO_MAX_UNITS = 24;

    /** Store of each city in the checkout "Select store" list: [lat, lng]. */
    public const STORES = [
        'Phnom Penh'    => [11.5527, 104.8337],
        'Siem Reap'     => [13.3633, 103.8564],
        'Battambang'    => [13.0957, 103.2022],
        'Sihanoukville' => [10.6093, 103.5296],
    ];

    /** Kg per one unit of a product, from its unit text. byWeight = sold loose by the kg. */
    public static function unitInfo(?string $unit): array
    {
        $u = strtolower(trim((string) $unit));
        if ($u === 'kg') {
            return ['kg' => 1.0, 'byWeight' => true];
        }
        if (preg_match('/(\d*\.?\d+)\s*(kg|g|ml|l)\b/', $u, $m)) {
            $n = (float) $m[1];
            $kg = match ($m[2]) { 'kg' => $n, 'g', 'ml' => $n / 1000, default => $n };   // 1 L ≈ 1 kg
            return ['kg' => $kg, 'byWeight' => false];
        }
        return ['kg' => 0.0, 'byWeight' => false];                                      // "each", "pack"...
    }

    /** @param iterable $products keyed by id  @param array $wanted [id => qty] */
    public static function load($products, array $wanted): array
    {
        $kg = 0.0;
        $units = 0.0;
        foreach ($wanted as $id => $qty) {
            $p = $products[$id] ?? null;
            if (! $p) {
                continue;
            }
            $info = self::unitInfo($p->unit);
            $kg += $info['kg'] * $qty;
            if (! $info['byWeight']) {
                $units += $qty;       // loose-by-kg items count by weight only
            }
        }
        return ['kg' => round($kg, 2), 'units' => round($units, 2)];
    }

    public static function motoAllowed(array $load): bool
    {
        return $load['kg'] <= self::MOTO_MAX_KG && $load['units'] <= self::MOTO_MAX_UNITS;
    }

    public static function haversineKm(array $a, array $b): float
    {
        $r = 6371;
        $dLat = deg2rad($b[0] - $a[0]);
        $dLng = deg2rad($b[1] - $a[1]);
        $h = sin($dLat / 2) ** 2 + cos(deg2rad($a[0])) * cos(deg2rad($b[0])) * sin($dLng / 2) ** 2;
        return 2 * $r * asin(min(1, sqrt($h)));
    }

    /** Road distance reported by the browser, kept inside a sane range of the straight-line distance. */
    public static function distanceKm(?string $city, $lat, $lng, $clientKm): ?float
    {
        if ($lat === null || $lng === null) {
            return null;
        }
        $store = self::STORES[$city] ?? self::STORES['Phnom Penh'];
        $straight = self::haversineKm($store, [(float) $lat, (float) $lng]);
        $km = $clientKm !== null ? (float) $clientKm : $straight * 1.3;
        return round(min(max($km, $straight), $straight * 3 + 1), 1);
    }

    public static function fee(string $type, ?float $km, Setting $settings): float
    {
        $base = (float) ($type === 'truck' ? $settings->truck_price : $settings->moto_price);
        $extra = $km !== null ? max(0, $km - self::FREE_KM) * self::PER_KM : 0;
        return round($base + $extra, 2);
    }
}
