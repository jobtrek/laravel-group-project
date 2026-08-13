<?php

namespace App\Models;

use App\Support\ResourceCap;
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

        return ResourceCap::capProgress($progress);
    }

    /**
     * How much may still be contributed towards $resource before hitting the cap.
     *
     * Pass $found explicitly when the caller holds a lock and must not read the
     * (possibly stale) loaded `contributions` relation.
     */
    public function remainingFor(PhaseResource $resource, ?float $found = null): float
    {
        $found ??= (float) $this->contributions
            ->where('resource_type', $resource->resource_type)
            ->sum('amount');

        return ResourceCap::remaining((float) $resource->amount_needed, $found);
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

    /**
     * Shape this phase (and its resources) into the nested array form
     * shared by the project edit and revision-correction forms.
     *
     * @return array{id: int|null, titre: string|null, duree: string|null, description: string|null, objectifs: array<int, string>, livrables: array<int, string>, ressources: array<int, array{id: int|null, resource_type: string|null, amount_needed: string}>}
     */
    public function toFormArray(): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->name,
            'duree' => $this->duration,
            'description' => $this->description,
            'objectifs' => $this->objectifs ?: [''],
            'livrables' => $this->livrables ?: [''],
            'ressources' => $this->resources->map(fn (PhaseResource $resource) => $resource->toFormArray())->all(),
        ];
    }
}
