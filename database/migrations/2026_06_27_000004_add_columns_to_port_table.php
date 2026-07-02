<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('PORT') && !Schema::hasColumn('PORT', 'port_code')) {
            DB::statement("
                ALTER TABLE PORT ADD (
                    port_code VARCHAR2(10) UNIQUE NOT NULL,
                    status VARCHAR2(20) DEFAULT 'ACTIVE'
                )
            ");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('PORT') && Schema::hasColumn('PORT', 'port_code')) {
            DB::statement("ALTER TABLE PORT DROP COLUMN port_code");
            DB::statement("ALTER TABLE PORT DROP COLUMN status");
        }
    }
};
