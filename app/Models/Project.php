<?php

namespace App\Models;

use App\Models\States\ArchiveState;
use App\Models\States\CompleteState;
use App\Models\States\EncoursState;
use App\Models\States\EvaluationState;
use App\Models\States\ProjectState;
use App\Models\States\PropositionState;
use App\Models\States\RecolteState;
use App\Support\ResourceCap;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\ModelStates\HasStates;

/**
 * @property Carbon|null $last_leader_comment_at Only present when the query used scopeNeedingProgressReminder().
 */
class Project extends Model
{
    use HasFactory;
    use HasStates;

    protected $fillable = [
        'title',
        'description',
        'but',
        'perimetre',
        'proposer_id',
        'leader_id',
        'recolte_manager_id',
        'ressources_totales',
    ];

    protected $casts = [
        'but' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'archived_at' => 'datetime',
        'restored_at' => 'datetime',
        'last_reminder_at' => 'datetime',
        'status' => ProjectState::class,
    ];

    /** @return BelongsTo<User, $this> */
    public function proposer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proposer_id');
    }

    /** @return BelongsTo<User, $this> */
    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    /** @return BelongsTo<User, $this> */
    public function recolteManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recolte_manager_id');
    }

    /** @return BelongsToMany<User, $this> */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members', 'project_id', 'user_id');
    }

    /** @return HasMany<Comment, $this> */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'project_id');
    }

    /** @return HasMany<ProjectReview, $this> */
    public function reviews(): HasMany
    {
        return $this->hasMany(ProjectReview::class, 'project_id');
    }

    /** @return HasMany<ProjectPhase, $this> */
    public function phases(): HasMany
    {
        return $this->hasMany(ProjectPhase::class, 'project_id')->orderBy('order');
    }

    /** @return HasOne<ProjectEvaluation, $this> */
    public function evaluation(): HasOne
    {
        return $this->hasOne(ProjectEvaluation::class, 'project_id');
    }

    public function getImportanceAttribute(): ?float
    {
        return $this->evaluation?->importance;
    }

    public function getProgressAttribute(): float
    {
        $totalNeeded = 0.0;
        $totalFound = 0.0;

        foreach ($this->phases as $phase) {
            $totalNeeded += $phase->amount_needed;
            $totalFound += $phase->amount_found;
        }

        if ($totalNeeded <= 0) {
            return 0.0;
        }

        $progress = round(($totalFound / $totalNeeded) * 100, 2);

        return ResourceCap::capProgress($progress);
    }

    public static function statusCounts()
    {
        return DB::table('projects')
            ->select('status')
            ->selectRaw('count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    /** @return HasManyThrough<ResourceContribution, ProjectPhase, $this> */
    public function resourceContributions(): HasManyThrough
    {
        return $this->hasManyThrough(
            ResourceContribution::class,
            ProjectPhase::class,
            'project_id', // FK on project_phases pointing back to projects
            'phase_id',   // FK on resource_contributions pointing to project_phases
            'id',         // local key on projects
            'id'          // local key on project_phases
        );
    }

    protected function budgetGlobal(): Attribute
    {
        return Attribute::make(
            get: function (): float {
                $totalNeeded = $this->loadMissing('phases.resources')->phases->sum('amount_needed');

                return $totalNeeded;
            }
        );
    }

    public function isArchived(): bool
    {
        return $this->status instanceof ArchiveState;
    }

    public function isCompleted(): bool
    {
        return $this->status instanceof CompleteState;
    }

    public function isProposition(): bool
    {
        return $this->status instanceof PropositionState;
    }

    public function isInEvaluation(): bool
    {
        return $this->status instanceof EvaluationState;
    }

    public function isInProgress(): bool
    {
        return $this->status instanceof EncoursState;
    }

    public function isInRecolte(): bool
    {
        return $this->status instanceof RecolteState;
    }

    public function canComment(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->can('comment', $this);
    }

    /**
     * @return array{bg: string, text: string, dot: string}|null
     */
    public function stalenessBadge(): ?array
    {
        if (! $this->updated_at) {
            return null;
        }

        $stalenessColors = config('projects.staleness_colors') ?? [];

        return match (true) {
            $this->updated_at->lessThan(now()->subMonths($stalenessColors['red_after_months'] ?? 3)) => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'dot' => 'bg-red-500'],
            $this->updated_at->lessThan(now()->subMonths($stalenessColors['orange_after_months'] ?? 2)) => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'dot' => 'bg-orange-500'],
            $this->updated_at->lessThan(now()->subMonths($stalenessColors['warning_after_months'] ?? 1)) => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-700', 'dot' => 'bg-yellow-500'],
            default => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'dot' => 'bg-green-500'],
        };
    }

    public function scopeNeedingProgressReminder(Builder $query): Builder
    {
        return $query
            ->whereState('status', EncoursState::class)
            ->whereNotNull('leader_id')
            ->addSelect([
                'last_leader_comment_at' => Comment::select('created_at')
                    ->whereColumn('comments.project_id', 'projects.id')
                    ->whereColumn('comments.user_id', 'projects.leader_id')
                    ->latest('created_at')
                    ->limit(1),
            ])
            ->withCasts(['last_leader_comment_at' => 'datetime']);
    }
}
