<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $guarded = ['id'];

    public function importBatch()
    {
        return $this->belongsTo(ImportBatch::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}