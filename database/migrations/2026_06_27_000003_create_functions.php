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

        DB::unprepared("
            CREATE OR REPLACE PROCEDURE book_shipment (
                p_customer_id IN NUMBER,
                p_source_port_id IN NUMBER,
                p_destination_port_id IN NUMBER,
                p_vehicle_id IN NUMBER,
                p_created_by IN NUMBER,
                p_expected_delivery_date IN DATE,
                p_notes IN VARCHAR2,
                p_shipment_id OUT NUMBER
            ) AS
            BEGIN
                INSERT INTO SHIPMENT (
                    customer_id,
                    source_port_id,
                    destination_port_id,
                    vehicle_id,
                    created_by,
                    shipment_ref,
                    status,
                    shipment_date,
                    expected_delivery_date,
                    notes
                ) VALUES (
                    p_customer_id,
                    p_source_port_id,
                    p_destination_port_id,
                    p_vehicle_id,
                    p_created_by,
                    'PENDING_REF',
                    'BOOKED',
                    SYSDATE,
                    p_expected_delivery_date,
                    p_notes
                ) RETURNING shipment_id INTO p_shipment_id;
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE PROCEDURE add_tracking_event (
                p_shipment_id IN NUMBER,
                p_port_id IN NUMBER,
                p_event_type IN VARCHAR2,
                p_location IN VARCHAR2,
                p_status IN VARCHAR2,
                p_remarks IN VARCHAR2
            ) AS
                v_final_status VARCHAR2(20);
            BEGIN
                INSERT INTO TRACKING_LOG (
                    shipment_id,
                    port_id,
                    event_type,
                    location,
                    status,
                    remarks,
                    updated_at
                ) VALUES (
                    p_shipment_id,
                    p_port_id,
                    p_event_type,
                    p_location,
                    p_status,
                    p_remarks,
                    SYSDATE
                );

                IF p_event_type IN ('PENDING', 'BOOKED', 'IN_TRANSIT', 'AT_PORT', 'DELIVERED', 'CANCELLED') THEN
                    v_final_status := p_event_type;
                ELSIF p_status IN ('PENDING', 'BOOKED', 'IN_TRANSIT', 'AT_PORT', 'DELIVERED', 'CANCELLED') THEN
                    v_final_status := p_status;
                END IF;

                IF v_final_status IS NOT NULL THEN
                    UPDATE SHIPMENT SET status = v_final_status WHERE shipment_id = p_shipment_id;

                    IF v_final_status IN ('DELIVERED', 'CANCELLED') THEN
                        UPDATE VEHICLE 
                        SET status = 'AVAILABLE' 
                        WHERE vehicle_id = (SELECT vehicle_id FROM SHIPMENT WHERE shipment_id = p_shipment_id);

                        UPDATE CONTAINER 
                        SET status = 'AVAILABLE' 
                        WHERE container_id IN (SELECT container_id FROM CONTAINER_ASSIGNMENT WHERE shipment_id = p_shipment_id);
                    END IF;

                    IF v_final_status = 'CANCELLED' THEN
                        UPDATE PAYMENT 
                        SET payment_status = 'REFUNDED' 
                        WHERE shipment_id = p_shipment_id;
                    END IF;
                END IF;
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE PROCEDURE deactivate_user (
                p_user_id IN NUMBER
            ) AS
            BEGIN
                UPDATE USERS 
                SET is_active = 'N' 
                WHERE user_id = p_user_id;
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE PROCEDURE create_payment (
                p_shipment_id IN NUMBER,
                p_customer_id IN NUMBER,
                p_amount IN NUMBER,
                p_payment_method IN VARCHAR2,
                p_due_date IN DATE,
                p_payment_id OUT NUMBER
            ) AS
                v_count NUMBER := 0;
            BEGIN
                IF p_amount <= 0 THEN
                    raise_application_error(-20001, 'Payment amount must be greater than zero.');
                END IF;

                SELECT COUNT(*) INTO v_count 
                FROM SHIPMENT 
                WHERE shipment_id = p_shipment_id AND customer_id = p_customer_id;
                
                IF v_count = 0 THEN
                    raise_application_error(-20002, 'Shipment does not exist or does not belong to the customer.');
                END IF;

                INSERT INTO PAYMENT (
                    shipment_id,
                    customer_id,
                    amount,
                    payment_method,
                    payment_status,
                    due_date
                ) VALUES (
                    p_shipment_id,
                    p_customer_id,
                    p_amount,
                    p_payment_method,
                    'PENDING',
                    p_due_date
                ) RETURNING payment_id INTO p_payment_id;
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE FUNCTION get_revenue (
                p_date_from IN DATE,
                p_date_to IN DATE
            ) RETURN NUMBER AS
                v_revenue NUMBER := 0;
            BEGIN
                SELECT NVL(SUM(amount), 0) INTO v_revenue
                FROM PAYMENT
                WHERE payment_status = 'COMPLETED'
                  AND payment_date >= p_date_from
                  AND p_date_to >= payment_date;
                RETURN v_revenue;
            END;
        ");
    }

    public function down(): void
    {
        try {
            DB::unprepared("DROP FUNCTION get_shipment_count");
        } catch (\Exception $e) {}

        try {
            DB::unprepared("DROP FUNCTION get_active_containers");
        } catch (\Exception $e) {}

        try {
            DB::unprepared("DROP PROCEDURE book_shipment");
        } catch (\Exception $e) {}

        try {
            DB::unprepared("DROP PROCEDURE add_tracking_event");
        } catch (\Exception $e) {}

        try {
            DB::unprepared("DROP PROCEDURE deactivate_user");
        } catch (\Exception $e) {}

        try {
            DB::unprepared("DROP PROCEDURE create_payment");
        } catch (\Exception $e) {}

        try {
            DB::unprepared("DROP FUNCTION get_revenue");
        } catch (\Exception $e) {}
    }
};
