<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
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

        DB::statement("
            CREATE OR REPLACE VIEW V_RECENT_SHIPMENTS AS
            SELECT
                s.shipment_id,
                s.customer_id,
                s.source_port_id,
                s.destination_port_id,
                s.vehicle_id,
                s.created_by,
                s.shipment_ref,
                s.status,
                s.shipment_date,
                s.expected_delivery_date,
                s.actual_delivery_date,
                s.notes,
                s.created_at,
                c.company_name,
                src.port_name as source_port,
                dst.port_name as destination_port
            FROM SHIPMENT s
            JOIN CUSTOMER c ON s.customer_id = c.customer_id
            JOIN PORT src   ON s.source_port_id = src.port_id
            JOIN PORT dst   ON s.destination_port_id = dst.port_id
        ");

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
    }

    public function down(): void
    {
        try {
            DB::statement("DROP VIEW V_ADMIN_DASHBOARD");
        } catch (\Exception $e) {}

        try {
            DB::statement("DROP VIEW V_RECENT_SHIPMENTS");
        } catch (\Exception $e) {}

        try {
            DB::statement("DROP VIEW V_CUSTOMER_DASHBOARD");
        } catch (\Exception $e) {}
    }
};
