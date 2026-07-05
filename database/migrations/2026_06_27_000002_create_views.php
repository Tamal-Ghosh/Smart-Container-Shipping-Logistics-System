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
                (SELECT COUNT(*) FROM SHIPMENT)                                     AS total_shipments,
                (SELECT COUNT(*) FROM SHIPMENT WHERE status='IN_TRANSIT')           AS active_shipments,
                (SELECT COUNT(*) FROM SHIPMENT WHERE status='DELIVERED')            AS delivered_shipments,
                (SELECT COUNT(*) FROM SHIPMENT WHERE status='CANCELLED')            AS cancelled_shipments,
                (SELECT COUNT(*) FROM CONTAINER WHERE status='AVAILABLE')           AS available_containers,
                (SELECT COUNT(*) FROM VEHICLE   WHERE status='AVAILABLE')           AS available_vehicles,
                (SELECT COUNT(*) FROM CUSTOMER)                                     AS total_customers,
                (SELECT NVL(SUM(amount),0) FROM PAYMENT WHERE payment_status='COMPLETED') AS total_revenue,
                (SELECT NVL(SUM(amount),0) FROM PAYMENT WHERE payment_status='REFUNDED')  AS refunded_amount
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

        DB::statement("
            CREATE OR REPLACE VIEW V_CONTAINER_UTILISATION AS
            SELECT
                c.container_id,
                c.container_number,
                c.container_type,
                c.status AS container_status,
                ca.assignment_id,
                ca.seal_number,
                ca.loaded_weight_kg,
                ca.assigned_at,
                s.shipment_id,
                s.shipment_ref,
                s.status AS shipment_status,
                cust.company_name AS customer_name
            FROM CONTAINER c
            LEFT JOIN CONTAINER_ASSIGNMENT ca ON c.container_id = ca.container_id
            LEFT JOIN SHIPMENT s ON ca.shipment_id = s.shipment_id
            LEFT JOIN CUSTOMER cust ON s.customer_id = cust.customer_id
        ");

        DB::statement("
            CREATE OR REPLACE VIEW V_LIVE_TRACKING AS
            SELECT
                tl.tracking_id,
                tl.shipment_id,
                s.shipment_ref,
                tl.port_id,
                p.port_name,
                tl.event_type,
                tl.location,
                tl.status AS tracking_status,
                tl.remarks,
                tl.updated_at
            FROM TRACKING_LOG tl
            JOIN SHIPMENT s ON tl.shipment_id = s.shipment_id
            LEFT JOIN PORT p ON tl.port_id = p.port_id
        ");

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

        try {
            DB::statement("DROP VIEW V_CONTAINER_UTILISATION");
        } catch (\Exception $e) {}

        try {
            DB::statement("DROP VIEW V_LIVE_TRACKING");
        } catch (\Exception $e) {}

        try {
            DB::statement("DROP VIEW V_PAYMENT_REPORT");
        } catch (\Exception $e) {}
    }
};
