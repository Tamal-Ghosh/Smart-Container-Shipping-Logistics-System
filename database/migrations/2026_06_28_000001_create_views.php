<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations to create the views currently used.
     */
    public function up(): void
    {
        // 1. Create/Replace V_ADMIN_DASHBOARD (Currently used by Admin DashboardController)
        DB::statement("
            CREATE OR REPLACE VIEW V_ADMIN_DASHBOARD AS
            SELECT
                (SELECT COUNT(*) FROM SHIPMENT)                           AS total_shipments,
                (SELECT COUNT(*) FROM SHIPMENT WHERE status='IN_TRANSIT') AS active_shipments,
                (SELECT COUNT(*) FROM SHIPMENT WHERE status='DELIVERED')  AS delivered_shipments,
                (SELECT COUNT(*) FROM CONTAINER WHERE status='AVAILABLE') AS available_containers,
                (SELECT COUNT(*) FROM VEHICLE   WHERE status='AVAILABLE') AS available_vehicles,
                (SELECT COUNT(*) FROM CUSTOMER)                           AS total_customers,
                (SELECT NVL(SUM(amount),0) FROM PAYMENT WHERE payment_status='COMPLETED') AS total_revenue
            FROM DUAL
        ");
    }

    /**
     * Reverse the migrations (Drop the view).
     */
    public function down(): void
    {
        DB::statement("DROP VIEW V_ADMIN_DASHBOARD");
    }
};
