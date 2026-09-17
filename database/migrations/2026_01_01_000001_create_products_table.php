<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->string('unit')->default('each');
            $table->string('image')->nullable();
            $table->string('emoji')->nullable();
            $table->unsignedInteger('qty')->default(0);
            $table->unsignedTinyInteger('discount_percent')->nullable();
            $table->date('date_added')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
