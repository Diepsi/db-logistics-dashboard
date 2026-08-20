<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InboundFirstMile extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'eta_pickup' => 'datetime',
        ];
    }

    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class);
    }
}
