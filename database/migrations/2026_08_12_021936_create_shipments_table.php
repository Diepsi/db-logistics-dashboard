<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')->constrained('import_batches')->onDelete('cascade');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null');
            
            // Nomor & Identitas Utama
            $table->string('waybill_no')->index(); // No Resi
            $table->string('manifest_no')->nullable()->index();
            $table->string('npsn')->nullable();
            $table->string('school_name')->nullable();
            $table->string('province')->nullable()->index();
            $table->string('city_regency')->nullable()->index();
            
            // Tanggal Operasional
            $table->dateTime('ho_date')->nullable();
            
            // SLA Pickup
            $table->dateTime('pickup_eta')->nullable();
            $table->string('pickup_sla_status')->nullable();
            $table->string('pickup_result')->nullable();
            
            // SLA Delivery
            $table->dateTime('delivery_eta')->nullable();
            $table->string('delivery_sla_status')->nullable();
            $table->string('delivery_result')->nullable();
            
            // SLA Last Mile (LM)
            $table->string('vendor_lm')->nullable();
            $table->string('lm_sla_status')->nullable();
            $table->string('lm_result')->nullable();
            
            // SLA Vendor
            $table->string('vendor_sla_status')->nullable();
            $table->string('vendor_result')->nullable();
            
            // Status Akhir
            $table->string('status_update')->nullable();
            $table->string('final_status')->default('On Delivery')->index();
            $table->boolean('is_within_sla')->default(true)->index();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};