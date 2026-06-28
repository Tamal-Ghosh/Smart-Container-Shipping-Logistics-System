<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to create the tables used so far.
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

                // Foreign key constraint (optional in migration since Oracle handles it, but good for visual code reference)
                // $table->foreign('user_id')->references('user_id')->on('USERS')->onDelete('cascade');
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

        // 4. SHIPMENT Table
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
    }

    /**
     * Reverse the migrations (Drop the tables).
     */
    public function down(): void
    {
        Schema::dropIfExists('SHIPMENT');
        Schema::dropIfExists('PORT');
        Schema::dropIfExists('CUSTOMER');
        Schema::dropIfExists('USERS');
    }
};
