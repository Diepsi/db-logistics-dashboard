<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('bast_status')->nullable()->after('is_within_sla');
            $table->dateTime('bast_date')->nullable()->after('bast_status');
            $table->string('finance_status')->nullable()->after('bast_date');
            $table->decimal('finance_amount', 15, 2)->nullable()->after('finance_status');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn([
                'bast_status',
                'bast_date',
                'finance_status',
                'finance_amount',
            ]);
        });
    }
};
