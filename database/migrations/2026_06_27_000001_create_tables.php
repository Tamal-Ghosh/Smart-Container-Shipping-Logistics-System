<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('USERS')) {
            DB::statement("
                CREATE TABLE USERS (
                    user_id NUMBER PRIMARY KEY,
                    username VARCHAR2(50) UNIQUE NOT NULL,
                    email VARCHAR2(100) UNIQUE NOT NULL,
                    password_hash VARCHAR2(255) NOT NULL,
                    role VARCHAR2(20) NOT NULL,
                    is_active CHAR(1) DEFAULT 'Y',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
        }

        if (!Schema::hasTable('CUSTOMER')) {
            DB::statement("
                CREATE TABLE CUSTOMER (
                    customer_id NUMBER PRIMARY KEY,
                    user_id NUMBER NOT NULL,
                    company_name VARCHAR2(100) NOT NULL,
                    contact_person VARCHAR2(100),
                    phone VARCHAR2(20),
                    email VARCHAR2(100),
                    address VARCHAR2(255),
                    country VARCHAR2(100),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
        }

        if (!Schema::hasTable('PORT')) {
            DB::statement("
                CREATE TABLE PORT (
                    port_id NUMBER PRIMARY KEY,
                    port_name VARCHAR2(100) NOT NULL,
                    location VARCHAR2(100),
                    country VARCHAR2(100)
                )
            ");
        }

        if (!Schema::hasTable('VEHICLE')) {
            DB::statement("
                CREATE TABLE VEHICLE (
                    vehicle_id NUMBER PRIMARY KEY,
                    vehicle_number VARCHAR2(50) UNIQUE NOT NULL,
                    type VARCHAR2(20) NOT NULL,
                    capacity_kg NUMBER(12, 2),
                    status VARCHAR2(20) DEFAULT 'AVAILABLE'
                )
            ");
        }

        if (!Schema::hasTable('CONTAINER')) {
            DB::statement("
                CREATE TABLE CONTAINER (
                    container_id NUMBER PRIMARY KEY,
                    container_number VARCHAR2(50) UNIQUE NOT NULL,
                    container_type VARCHAR2(20) NOT NULL,
                    status VARCHAR2(20) DEFAULT 'AVAILABLE'
                )
            ");
        }

        if (!Schema::hasTable('SHIPMENT')) {
            DB::statement("
                CREATE TABLE SHIPMENT (
                    shipment_id NUMBER PRIMARY KEY,
                    customer_id NUMBER NOT NULL,
                    source_port_id NUMBER NOT NULL,
                    destination_port_id NUMBER NOT NULL,
                    vehicle_id NUMBER,
                    created_by NUMBER NOT NULL,
                    shipment_ref VARCHAR2(30) UNIQUE NOT NULL,
                    status VARCHAR2(20) DEFAULT 'BOOKED',
                    shipment_date TIMESTAMP,
                    expected_delivery_date TIMESTAMP,
                    actual_delivery_date TIMESTAMP,
                    notes VARCHAR2(500),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
        }

        if (!Schema::hasTable('CONTAINER_ASSIGNMENT')) {
            DB::statement("
                CREATE TABLE CONTAINER_ASSIGNMENT (
                    assignment_id NUMBER PRIMARY KEY,
                    shipment_id NUMBER NOT NULL,
                    container_id NUMBER NOT NULL,
                    seal_number VARCHAR2(50),
                    loaded_weight_kg NUMBER(10, 2),
                    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
        }

        if (!Schema::hasTable('PAYMENT')) {
            DB::statement("
                CREATE TABLE PAYMENT (
                    payment_id NUMBER PRIMARY KEY,
                    shipment_id NUMBER NOT NULL,
                    customer_id NUMBER NOT NULL,
                    amount NUMBER(12, 2) NOT NULL,
                    payment_method VARCHAR2(30),
                    payment_status VARCHAR2(20) DEFAULT 'PENDING',
                    payment_date TIMESTAMP,
                    transaction_ref VARCHAR2(50) UNIQUE,
                    due_date TIMESTAMP
                )
            ");
        }

        if (!Schema::hasTable('TRACKING_LOG')) {
            DB::statement("
                CREATE TABLE TRACKING_LOG (
                    tracking_id NUMBER PRIMARY KEY,
                    shipment_id NUMBER NOT NULL,
                    port_id NUMBER,
                    event_type VARCHAR2(30),
                    location VARCHAR2(100),
                    status VARCHAR2(30),
                    remarks VARCHAR2(255),
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
        }
    }

    public function down(): void
    {
        $tables = [
            'TRACKING_LOG', 'PAYMENT', 'CONTAINER_ASSIGNMENT', 'SHIPMENT',
            'CONTAINER', 'VEHICLE', 'PORT', 'CUSTOMER', 'USERS'
        ];

        foreach ($tables as $table) {
            try {
                DB::statement("DROP TABLE {$table}");
            } catch (\Exception $e) {}
        }
    }
};
