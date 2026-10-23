<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Promotions used to copy their percent onto products.discount_percent and
     * keep it there forever. Promotion discounts are now read live from the
     * promotion (respecting its dates), so remove those copied values —
     * only where the product's percent still equals its promotion's percent.
     */
    public function up(): void
    {
        $rows = DB::table('promotion_product')
            ->join('promotions', 'promotions.id', '=', 'promotion_product.promotion_id')
            ->select('promotion_product.product_id', 'promotions.discount_percent')
            ->get();

        foreach ($rows as $row) {
            DB::table('products')
                ->where('id', $row->product_id)
                ->where('discount_percent', $row->discount_percent)
                ->update(['discount_percent' => null]);
        }
    }

    public function down(): void
    {
        // Not reversible: the copied values were redundant.
    }
};
