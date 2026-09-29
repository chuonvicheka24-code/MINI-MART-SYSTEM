<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->unsignedTinyInteger('discount_percent')->nullable()->after('description');
            $table->date('starts_at')->nullable()->after('discount_percent');
            $table->date('ends_at')->nullable()->after('starts_at');
            $table->timestamp('ended_at')->nullable()->after('ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn(['discount_percent', 'starts_at', 'ends_at', 'ended_at']);
        });
    }
};
