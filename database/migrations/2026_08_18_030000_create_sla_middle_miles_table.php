<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sla_middle_miles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')->constrained()->onDelete('cascade');
            $table->string('waybill_no')->unique();
            $table->string('vendor_mm')->nullable();
            $table->dateTime('eta_mm')->nullable();
            $table->string('sla_mm')->nullable();
            $table->string('result_mm')->nullable();
            $table->dateTime('tgl_sampai_kota_tujuan')->nullable();
            $table->string('province')->nullable();
            $table->string('city_regency')->nullable();
            $table->string('npsn')->nullable();
            $table->string('school_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sla_middle_miles');
    }
};
