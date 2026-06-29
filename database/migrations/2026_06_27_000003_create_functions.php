<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("
            CREATE OR REPLACE FUNCTION get_shipment_count(p_customer_id IN NUMBER) RETURN NUMBER AS
                v_count NUMBER := 0;
            BEGIN
                SELECT COUNT(*) INTO v_count
                FROM SHIPMENT
                WHERE customer_id = p_customer_id;
                RETURN v_count;
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE FUNCTION get_active_containers RETURN NUMBER AS
                v_count NUMBER := 0;
            BEGIN
                SELECT COUNT(*) INTO v_count
                FROM CONTAINER
                WHERE status <> 'AVAILABLE';
                RETURN v_count;
            END;
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP FUNCTION get_shipment_count");
        DB::unprepared("DROP FUNCTION get_active_containers");
    }
};
