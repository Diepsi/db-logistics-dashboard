<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbound_first_miles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')->constrained()->onDelete('cascade');
            $table->string('waybill_no')->unique();
            $table->string('manifest_no')->nullable();
            $table->dateTime('eta_pickup')->nullable();
            $table->string('status_inbound')->nullable();
            $table->string('vendor_lm')->nullable();
            $table->string('province')->nullable();
            $table->string('city_regency')->nullable();
            $table->string('npsn')->nullable();
            $table->string('school_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbound_first_miles');
    }
};
