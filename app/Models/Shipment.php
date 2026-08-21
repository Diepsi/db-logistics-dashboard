<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'ho_date' => 'datetime',
            'pickup_eta' => 'datetime',
            'delivery_eta' => 'datetime',
            'is_within_sla' => 'boolean',
        ];
    }

    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(ShipmentIssue::class);
    }

    public function inboundFirstMile(): HasOne
    {
        return $this->hasOne(InboundFirstMile::class, 'waybill_no', 'waybill_no');
    }

    public function slaMiddleMile(): HasOne
    {
        return $this->hasOne(SlaMiddleMile::class, 'waybill_no', 'waybill_no');
    }

    public function slaLastMile(): HasOne
    {
        return $this->hasOne(SlaLastMile::class, 'waybill_no', 'waybill_no');
    }
}
