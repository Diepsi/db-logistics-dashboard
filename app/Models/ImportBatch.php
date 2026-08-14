<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportBatch extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}
