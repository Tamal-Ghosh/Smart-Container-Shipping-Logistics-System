<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations to create/replace all Oracle Database Views.
     */
    public function up(): void
    {
        // 1. Create/Replace V_ADMIN_DASHBOARD
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

        // 2. Create/Replace V_CUSTOMER_DASHBOARD
        DB::statement("
            CREATE OR REPLACE VIEW V_CUSTOMER_DASHBOARD AS
            SELECT
                c.customer_id,
                c.company_name,
                s.shipment_id,
                s.shipment_ref,
                s.status            AS shipment_status,
                s.shipment_date,
                s.expected_delivery_date,
                s.actual_delivery_date,
                p_src.port_name     AS source_port,
                p_dst.port_name     AS destination_port,
                py.amount           AS payment_amount,
                py.payment_status
            FROM CUSTOMER c
            JOIN SHIPMENT s  ON s.customer_id  = c.customer_id
            JOIN PORT p_src  ON p_src.port_id  = s.source_port_id
            JOIN PORT p_dst  ON p_dst.port_id  = s.destination_port_id
            LEFT JOIN PAYMENT py ON py.shipment_id = s.shipment_id
        ");

        // 3. Create/Replace V_LIVE_TRACKING
        DB::statement("
            CREATE OR REPLACE VIEW V_LIVE_TRACKING AS
            SELECT
                s.shipment_ref,
                s.status            AS shipment_status,
                c.company_name      AS customer,
                tl.event_type,
                tl.location,
                tl.status           AS tracking_status,
                tl.updated_at,
                tl.remarks,
                p.port_name         AS event_port
            FROM TRACKING_LOG tl
            JOIN SHIPMENT s  ON s.shipment_id = tl.shipment_id
            JOIN CUSTOMER c  ON c.customer_id = s.customer_id
            LEFT JOIN PORT p ON p.port_id     = tl.port_id
            ORDER BY tl.updated_at DESC
        ");

        // 4. Create/Replace V_PAYMENT_REPORT
        DB::statement("
            CREATE OR REPLACE VIEW V_PAYMENT_REPORT AS
            SELECT
                py.payment_id,
                s.shipment_ref,
                c.company_name,
                py.amount,
                py.payment_method,
                py.payment_status,
                py.payment_date,
                py.transaction_ref,
                py.due_date
            FROM PAYMENT py
            JOIN SHIPMENT s  ON s.shipment_id  = py.shipment_id
            JOIN CUSTOMER c  ON c.customer_id  = py.customer_id
        ");

        // 5. Create/Replace V_CONTAINER_UTILISATION
        DB::statement("
            CREATE OR REPLACE VIEW V_CONTAINER_UTILISATION AS
            SELECT
                cn.container_id,
                cn.container_number,
                cn.container_type,
                cn.status,
                s.shipment_ref,
                s.status        AS shipment_status,
                ca.seal_number,
                ca.loaded_weight_kg,
                ca.assigned_at
            FROM CONTAINER cn
            LEFT JOIN CONTAINER_ASSIGNMENT ca ON ca.container_id = cn.container_id
            LEFT JOIN SHIPMENT s               ON s.shipment_id   = ca.shipment_id
        ");
    }

    /**
     * Reverse the migrations (Drop all Views).
     */
    public function down(): void
    {
        DB::statement("DROP VIEW V_ADMIN_DASHBOARD");
        DB::statement("DROP VIEW V_CUSTOMER_DASHBOARD");
        DB::statement("DROP VIEW V_LIVE_TRACKING");
        DB::statement("DROP VIEW V_PAYMENT_REPORT");
        DB::statement("DROP VIEW V_CONTAINER_UTILISATION");
    }
};
