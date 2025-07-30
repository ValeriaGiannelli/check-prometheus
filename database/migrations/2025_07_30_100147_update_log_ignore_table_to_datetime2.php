<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE log_ignore ALTER COLUMN created_at DATETIME2(7) NULL');
        DB::statement('ALTER TABLE log_ignore ALTER COLUMN updated_at DATETIME2(7) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE log_ignore ALTER COLUMN created_at DATETIME NULL');
        DB::statement('ALTER TABLE log_ignore ALTER COLUMN updated_at DATETIME NULL');
    }
};
