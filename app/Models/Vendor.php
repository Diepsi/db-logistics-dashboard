<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $guarded = ['id'];

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}