<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('location')->nullable();
            $table->string('address')->nullable();
            $table->string('truck_number')->nullable();
            $table->enum('transport_type', ['truck', 'moto'])->default('truck');
            $table->string('payment_method')->default('Cash on delivery');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('status', ['pending', 'out', 'done'])->default('pending');
            $table->foreignId('delivery_staff_id')->nullable()->constrained('delivery_staff')->nullOnDelete();
            $table->timestamp('placed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('unit')->nullable();
            $table->decimal('price', 8, 2);
            
            // CHANGED: From unsignedInteger to decimal(8,2) to support 1.5, 0.5, etc.
            $table->decimal('qty', 8, 2); 
            
            $table->decimal('line_total', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};