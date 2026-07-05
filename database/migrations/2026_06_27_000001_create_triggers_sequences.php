<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $sequences = [
            'SEQ_USER', 'SEQ_CUSTOMER', 'SEQ_PORT', 'SEQ_VEHICLE',
            'SEQ_CONTAINER', 'SEQ_SHIPMENT', 'SEQ_ASSIGNMENT',
            'SEQ_PAYMENT', 'SEQ_TRACKING'
        ];

        foreach ($sequences as $seq) {
            try {
                DB::unprepared("CREATE SEQUENCE {$seq} START WITH 1 INCREMENT BY 1");
            } catch (\Exception $e) {}
        }

        DB::unprepared("
            CREATE OR REPLACE TRIGGER trg_users_pk
            BEFORE INSERT ON USERS
            FOR EACH ROW
            WHEN (NEW.USER_ID IS NULL)
            BEGIN
                SELECT SEQ_USER.NEXTVAL INTO :NEW.USER_ID FROM DUAL;
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE TRIGGER trg_customer_pk
            BEFORE INSERT ON CUSTOMER
            FOR EACH ROW
            WHEN (NEW.CUSTOMER_ID IS NULL)
            BEGIN
                SELECT SEQ_CUSTOMER.NEXTVAL INTO :NEW.CUSTOMER_ID FROM DUAL;
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE TRIGGER trg_port_pk
            BEFORE INSERT ON PORT
            FOR EACH ROW
            WHEN (NEW.PORT_ID IS NULL)
            BEGIN
                SELECT SEQ_PORT.NEXTVAL INTO :NEW.PORT_ID FROM DUAL;
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE TRIGGER trg_vehicle_pk
            BEFORE INSERT ON VEHICLE
            FOR EACH ROW
            WHEN (NEW.VEHICLE_ID IS NULL)
            BEGIN
                SELECT SEQ_VEHICLE.NEXTVAL INTO :NEW.VEHICLE_ID FROM DUAL;
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE TRIGGER trg_container_pk
            BEFORE INSERT ON CONTAINER
            FOR EACH ROW
            WHEN (NEW.CONTAINER_ID IS NULL)
            BEGIN
                SELECT SEQ_CONTAINER.NEXTVAL INTO :NEW.CONTAINER_ID FROM DUAL;
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE TRIGGER trg_shipment_pk
            BEFORE INSERT ON SHIPMENT
            FOR EACH ROW
            BEGIN
                IF :NEW.SHIPMENT_ID IS NULL THEN
                    SELECT SEQ_SHIPMENT.NEXTVAL INTO :NEW.SHIPMENT_ID FROM DUAL;
                END IF;
                IF :NEW.SHIPMENT_REF IS NULL OR :NEW.SHIPMENT_REF = 'PENDING_REF' THEN
                    :NEW.SHIPMENT_REF := 'SHP-' || TO_CHAR(SYSDATE, 'YYYY') || '-' || LPAD(TO_CHAR(:NEW.SHIPMENT_ID), 5, '0');
                END IF;
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE TRIGGER trg_assignment_pk
            BEFORE INSERT ON CONTAINER_ASSIGNMENT
            FOR EACH ROW
            WHEN (NEW.ASSIGNMENT_ID IS NULL)
            BEGIN
                SELECT SEQ_ASSIGNMENT.NEXTVAL INTO :NEW.ASSIGNMENT_ID FROM DUAL;
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE TRIGGER trg_payment_pk
            BEFORE INSERT ON PAYMENT
            FOR EACH ROW
            WHEN (NEW.PAYMENT_ID IS NULL)
            BEGIN
                SELECT SEQ_PAYMENT.NEXTVAL INTO :NEW.PAYMENT_ID FROM DUAL;
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE TRIGGER trg_tracking_pk
            BEFORE INSERT ON TRACKING_LOG
            FOR EACH ROW
            WHEN (NEW.TRACKING_ID IS NULL)
            BEGIN
                SELECT SEQ_TRACKING.NEXTVAL INTO :NEW.TRACKING_ID FROM DUAL;
            END;
        ");
    }

    public function down(): void
    {
        $triggers = [
            'trg_users_pk', 'trg_customer_pk', 'trg_port_pk', 'trg_vehicle_pk',
            'trg_container_pk', 'trg_shipment_pk', 'trg_assignment_pk',
            'trg_payment_pk', 'trg_tracking_pk'
        ];

        foreach ($triggers as $trg) {
            try {
                DB::unprepared("DROP TRIGGER {$trg}");
            } catch (\Exception $e) {}
        }

        $sequences = [
            'SEQ_USER', 'SEQ_CUSTOMER', 'SEQ_PORT', 'SEQ_VEHICLE',
            'SEQ_CONTAINER', 'SEQ_SHIPMENT', 'SEQ_ASSIGNMENT',
            'SEQ_PAYMENT', 'SEQ_TRACKING'
        ];

        foreach ($sequences as $seq) {
            try {
                DB::unprepared("DROP SEQUENCE {$seq}");
            } catch (\Exception $e) {}
        }
    }
};
