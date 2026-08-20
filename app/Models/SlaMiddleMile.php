<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlaMiddleMile extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'eta_mm' => 'datetime',
            'tgl_sampai_kota_tujuan' => 'datetime',
        ];
    }

    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class);
    }
}
