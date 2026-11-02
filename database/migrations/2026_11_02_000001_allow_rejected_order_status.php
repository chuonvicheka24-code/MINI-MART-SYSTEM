<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * orders.status was created as ENUM('pending','out','done'), which makes the
     * database reject the 'rejected' status that Admin\OrderController::reject() sets.
     * A plain string column accepts it (and keeps the same default).
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }

    public function down(): void
    {
        // Intentionally empty: narrowing back to the 3-value enum would fail
        // as soon as one 'rejected' order exists.
    }
};
