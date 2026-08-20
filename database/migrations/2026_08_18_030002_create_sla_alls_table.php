<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sla_alls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')->constrained()->onDelete('cascade');
            $table->string('waybill_no')->unique();
            $table->string('sla_overall')->nullable();
            $table->string('result_overall')->nullable();
            $table->string('vendor_lm')->nullable();
            $table->string('vendor_mm')->nullable();
            $table->string('province')->nullable();
            $table->string('city_regency')->nullable();
            $table->dateTime('eta_delivery')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sla_alls');
    }
};
