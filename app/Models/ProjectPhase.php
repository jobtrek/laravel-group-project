<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectPhase extends Model
{
    use HasFactory;

    protected $table = 'project_phases';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'duration',
        'description',
        'objectifs',
        'livrables',
        'order',
        'project_id',
    ];

    protected $casts = [
        'objectifs' => 'array',
        'livrables' => 'array',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /** @return HasMany<PhaseResource, $this> */
    public function resources(): HasMany
    {
        return $this->hasMany(PhaseResource::class, 'phase_id');
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(ResourceContribution::class, 'phase_id');
    }

    public function getAmountNeededAttribute(): float
    {
        return (float) $this->resources->sum('amount_needed');
    }

    public function getAmountFoundAttribute(): float
    {
        return (float) $this->contributions->sum('amount');
    }

    public function getProgressAttribute(): float
    {
        if ($this->amount_needed <= 0) {
            return 0.0;
        }

        $progress = round(($this->amount_found / $this->amount_needed) * 100, 2);

        return max(0.0, min($progress, 200.0));
    }

    /** @return HasMany<PhaseItemCompletion, $this> */
    public function itemCompletions(): HasMany
    {
        return $this->hasMany(PhaseItemCompletion::class, 'phase_id');
    }

    public function isItemCompleted(string $itemType, int $itemIndex): bool
    {
        $completion = $this->itemCompletions
            ->where('item_type', $itemType)
            ->where('item_index', $itemIndex)
            ->first();

        if (! $completion instanceof PhaseItemCompletion) {
            return false;
        }

        return $completion->completed;
    }

    public function isResourceComplete(PhaseResource $resource): bool
    {
        $found = (float) $this->contributions
            ->where('resource_type', $resource->resource_type)
            ->sum('amount');

        return $found >= (float) $resource->amount_needed;
    }

    public function getResourceQuantityString(PhaseResource $resource): string
    {
        $found = (float) $this->contributions
            ->where('resource_type', $resource->resource_type)
            ->sum('amount');

        return number_format($found, 2).' / '.number_format((float) $resource->amount_needed, 2).' CHF';
    }
}
