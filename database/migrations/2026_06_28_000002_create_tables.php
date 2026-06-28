<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to create all database tables.
     */
    public function up(): void
    {
        // 1. USERS Table
        if (!Schema::hasTable('USERS')) {
            Schema::create('USERS', function (Blueprint $table) {
                $table->integer('user_id')->primary(); // Oracle trigger will populate this from SEQ_USER
                $table->string('username', 50)->unique();
                $table->string('email', 100)->unique();
                $table->string('password_hash', 255);
                $table->string('role', 20); // ADMIN, CUSTOMER, OPERATOR
                $table->char('is_active', 1)->default('Y');
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // 2. CUSTOMER Table
        if (!Schema::hasTable('CUSTOMER')) {
            Schema::create('CUSTOMER', function (Blueprint $table) {
                $table->integer('customer_id')->primary(); // Oracle trigger will populate this from SEQ_CUSTOMER
                $table->integer('user_id');
                $table->string('company_name', 100);
                $table->string('contact_person', 100)->nullable();
                $table->string('phone', 20)->nullable();
                $table->string('email', 100)->nullable();
                $table->string('address', 255)->nullable();
                $table->string('country', 100)->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // 3. PORT Table
        if (!Schema::hasTable('PORT')) {
            Schema::create('PORT', function (Blueprint $table) {
                $table->integer('port_id')->primary();
                $table->string('port_name', 100);
                $table->string('location', 100)->nullable();
                $table->string('country', 100)->nullable();
            });
        }

        // 4. VEHICLE Table
        if (!Schema::hasTable('VEHICLE')) {
            Schema::create('VEHICLE', function (Blueprint $table) {
                $table->integer('vehicle_id')->primary();
                $table->string('vehicle_number', 50)->unique();
                $table->string('type', 20); // TRUCK, VESSEL, TRAIN
                $table->decimal('capacity_kg', 12, 2)->nullable();
                $table->string('status', 20)->default('AVAILABLE');
            });
        }

        // 5. CONTAINER Table
        if (!Schema::hasTable('CONTAINER')) {
            Schema::create('CONTAINER', function (Blueprint $table) {
                $table->integer('container_id')->primary();
                $table->string('container_number', 50)->unique();
                $table->string('container_type', 20); // STANDARD, REEFER, OPEN_TOP
                $table->string('status', 20)->default('AVAILABLE');
            });
        }

        // 6. SHIPMENT Table
        if (!Schema::hasTable('SHIPMENT')) {
            Schema::create('SHIPMENT', function (Blueprint $table) {
                $table->integer('shipment_id')->primary();
                $table->integer('customer_id');
                $table->integer('source_port_id');
                $table->integer('destination_port_id');
                $table->integer('vehicle_id')->nullable();
                $table->integer('created_by');
                $table->string('shipment_ref', 30)->unique();
                $table->string('status', 20)->default('BOOKED');
                $table->timestamp('shipment_date')->nullable();
                $table->timestamp('expected_delivery_date')->nullable();
                $table->timestamp('actual_delivery_date')->nullable();
                $table->string('notes', 500)->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // 7. CONTAINER_ASSIGNMENT Table
        if (!Schema::hasTable('CONTAINER_ASSIGNMENT')) {
            Schema::create('CONTAINER_ASSIGNMENT', function (Blueprint $table) {
                $table->integer('assignment_id')->primary();
                $table->integer('shipment_id');
                $table->integer('container_id');
                $table->string('seal_number', 50)->nullable();
                $table->decimal('loaded_weight_kg', 10, 2)->nullable();
                $table->timestamp('assigned_at')->useCurrent();
            });
        }

        // 8. PAYMENT Table
        if (!Schema::hasTable('PAYMENT')) {
            Schema::create('PAYMENT', function (Blueprint $table) {
                $table->integer('payment_id')->primary();
                $table->integer('shipment_id');
                $table->integer('customer_id');
                $table->decimal('amount', 12, 2);
                $table->string('payment_method', 30)->nullable();
                $table->string('payment_status', 20)->default('PENDING');
                $table->timestamp('payment_date')->nullable();
                $table->string('transaction_ref', 50)->unique()->nullable();
                $table->timestamp('due_date')->nullable();
            });
        }

        // 9. TRACKING_LOG Table
        if (!Schema::hasTable('TRACKING_LOG')) {
            Schema::create('TRACKING_LOG', function (Blueprint $table) {
                $table->integer('tracking_id')->primary();
                $table->integer('shipment_id');
                $table->integer('port_id')->nullable();
                $table->string('event_type', 30)->nullable();
                $table->string('location', 100)->nullable();
                $table->string('status', 30)->nullable();
                $table->string('remarks', 255)->nullable();
                $table->timestamp('updated_at')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations (Drop the tables).
     */
    public function down(): void
    {
        Schema::dropIfExists('TRACKING_LOG');
        Schema::dropIfExists('PAYMENT');
        Schema::dropIfExists('CONTAINER_ASSIGNMENT');
        Schema::dropIfExists('SHIPMENT');
        Schema::dropIfExists('CONTAINER');
        Schema::dropIfExists('VEHICLE');
        Schema::dropIfExists('PORT');
        Schema::dropIfExists('CUSTOMER');
        Schema::dropIfExists('USERS');
    }
};
