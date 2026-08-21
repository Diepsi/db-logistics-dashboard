<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->index('ho_date');
            $table->index('vendor_lm');
            $table->index('bast_status');
            $table->index('finance_status');
            $table->index(['province', 'city_regency']);
            $table->index(['final_status', 'is_within_sla']);
            $table->index(['final_status', 'ho_date']);
            $table->index(['vendor_lm', 'is_within_sla']);
        });

        Schema::table('sla_middle_miles', function (Blueprint $table) {
            $table->index('vendor_mm');
            $table->index('result_mm');
        });

        Schema::table('sla_last_miles', function (Blueprint $table) {
            $table->index('vendor_lm');
            $table->index('result_lm');
        });

        Schema::table('inbound_first_miles', function (Blueprint $table) {
            $table->index('status_inbound');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropIndex(['ho_date']);
            $table->dropIndex(['vendor_lm']);
            $table->dropIndex(['bast_status']);
            $table->dropIndex(['finance_status']);
            $table->dropIndex(['province', 'city_regency']);
            $table->dropIndex(['final_status', 'is_within_sla']);
            $table->dropIndex(['final_status', 'ho_date']);
            $table->dropIndex(['vendor_lm', 'is_within_sla']);
        });

        Schema::table('sla_middle_miles', function (Blueprint $table) {
            $table->dropIndex(['vendor_mm']);
            $table->dropIndex(['result_mm']);
        });

        Schema::table('sla_last_miles', function (Blueprint $table) {
            $table->dropIndex(['vendor_lm']);
            $table->dropIndex(['result_lm']);
        });

        Schema::table('inbound_first_miles', function (Blueprint $table) {
            $table->dropIndex(['status_inbound']);
        });
    }
};
