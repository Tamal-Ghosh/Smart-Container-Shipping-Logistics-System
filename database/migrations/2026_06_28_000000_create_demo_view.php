<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations to create the Oracle Database View.
     */
    public function up(): void
    {
        // Laravel-এর ভেতর থেকে Raw SQL ব্যবহার করে V_DEMO_USERS নামের ভিউ তৈরি করা
        DB::statement("
            CREATE OR REPLACE VIEW V_DEMO_USERS AS
            SELECT user_id, username, email, role, created_at
            FROM USERS
            WHERE is_active = 'Y'
        ");
    }

    /**
     * Reverse the migrations (Drop the View).
     */
    public function down(): void
    {
        // মাইগ্রেশন রোলব্যাক করার সময় ভিউটি ড্রপ (ডিলেট) করা
        DB::statement("DROP VIEW V_DEMO_USERS");
    }
};
