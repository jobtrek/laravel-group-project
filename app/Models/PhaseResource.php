<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhaseResource extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'resource_type', 'description', 'work_rate',
        'amount_needed', 'amount_found', 'phase_id',
    ];

    protected $casts = [
        'work_rate' => 'integer',
        'amount_needed' => 'decimal:2',
        'amount_found' => 'decimal:2',
    ];

    public function phase(): BelongsTo
    {
        return $this->belongsTo(ProjectPhase::class);
    }
}
