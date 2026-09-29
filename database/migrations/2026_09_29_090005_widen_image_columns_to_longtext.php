<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The "Add new item" form uploads a photo as a base64 data URL, which is
     * far longer than the 255-char VARCHAR these columns started as — widen
     * both to LONGTEXT so a real photo upload doesn't get truncated/rejected.
     *
     * Raw SQL is used here (instead of ->change()) so this doesn't require
     * the optional doctrine/dbal package to be installed.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite has no real column-size limit for TEXT, so there is
            // nothing to widen — base64 strings already fit as-is.
            return;
        }

        DB::statement('ALTER TABLE `products` MODIFY `image` LONGTEXT NULL');
        DB::statement('ALTER TABLE `categories` MODIFY `image` LONGTEXT NULL');
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE `products` MODIFY `image` VARCHAR(255) NULL');
        DB::statement('ALTER TABLE `categories` MODIFY `image` VARCHAR(255) NULL');
    }
};
