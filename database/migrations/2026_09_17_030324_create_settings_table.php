<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->default('Mini Mart');
            $table->string('hours')->default('7:00 AM – 10:00 PM');
            $table->decimal('delivery_rate', 5, 2)->default(10);
            $table->unsignedInteger('low_stock_threshold')->default(10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};