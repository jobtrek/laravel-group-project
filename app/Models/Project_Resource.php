<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhaseResource extends Model
{
    use HasFactory;

    protected $table = 'phase_resources';

    public $timestamps = false;

    protected $fillable = [
        'resource_type',
        'description',
        'work_rate',
        'amount_needed',
        'amount_found',
        'phase_id',
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