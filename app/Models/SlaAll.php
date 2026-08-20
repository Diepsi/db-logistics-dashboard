<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlaAll extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'eta_delivery' => 'datetime',
        ];
    }

    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class);
    }
}
