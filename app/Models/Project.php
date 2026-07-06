<?php

namespace App\Models;

use App\Enums\Role;
use App\Models\States\EncoursState;
use App\Models\States\ProjectState;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Spatie\ModelStates\HasStates;

class Project extends Model
{
    use HasFactory;
    use HasStates;

    protected $fillable = [
        'title',
        'description',
        'budget_global',
        'but',
        'perimetre',
        'status',
        'current_stage',
        'archived_at',
        'restored_at',
        'last_reminder_at',
        'proposer_id',
        'leader_id',
        'recolte_manager_id',
        'ressources_totales',
    ];

    protected $casts = [
        'budget_global' => 'decimal:2',
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

        return max(0.0, min($progress, 100.0));
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

    public function canComment(?User $user): bool
    {
        return $this->status instanceof EncoursState
            && (bool) $user?->hasRole(Role::ChefDeProjet->value)
            && $user?->id === $this->leader_id;
    }

    public function scopeNeedingProgressReminder(Builder $query): Builder
    {
        return $query
            ->whereState('status', EncoursState::class)
            ->whereNotNull('leader_id')
            ->addSelect(['last_leader_comment_at' => Comment::select('created_at')
                ->whereColumn('comments.project_id', 'projects.id')
                ->whereColumn('comments.user_id', 'projects.leader_id')
                ->latest('created_at')
                ->limit(1),
            ])
            ->withCasts(['last_leader_comment_at' => 'datetime']);
    }
}
