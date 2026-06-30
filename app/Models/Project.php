<?php

namespace App\Models;

use App\Models\States\ProjectState;
use App\Models\States\PropositionState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members', 'project_id', 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'project_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProjectReview::class, 'project_id');
    }

    /** @return HasMany<ProjectPhase, $this> */
    public function phases(): HasMany
    {
        return $this->hasMany(ProjectPhase::class, 'project_id')->orderBy('order');
    }

    public function evaluation(): HasOne
    {
        return $this->hasOne(ProjectEvaluation::class, 'project_id');
    }

    public function getImportanceAttribute(): ?float
    {
        return $this->evaluation?->importance;
    }

    public static function createProposal(array $data, int $proposerId): self
    {
        return DB::transaction(function () use ($data, $proposerId) {
            $project = self::create([
                'title' => $data['titre'],
                'description' => $data['description'],
                'but' => $data['buts'],
                'perimetre' => $data['perimetre'] ?? null,
                'ressources_totales' => $data['ressources_totales'] ?? null,
                'status' => PropositionState::getMorphClass(),
                'current_stage' => PropositionState::getMorphClass(),
                'proposer_id' => $proposerId,
                'leader_id' => $data['porteur'],
            ]);

            $project->members()->attach($data['membres']);

            $project->evaluation()->create([
                'portee' => $data['portee'],
                'impact' => $data['impact'],
                'confiance' => $data['confiance'],
                'effort' => $data['effort'],
            ]);

            foreach ($data['phases'] as $index => $phase) {
                /** @var ProjectPhase $createdPhase */
                $createdPhase = $project->phases()->create([
                    'name' => $phase['titre'],
                    'duration' => $phase['duree'],
                    'description' => $phase['description'],
                    'objectifs' => $phase['objectifs'],
                    'livrables' => $phase['livrables'],
                    'order' => $index + 1,
                ]);

                foreach ($phase['ressources_necessaires'] as $resource) {
                    $createdPhase->resources()->create([
                        'resource_type' => $resource['resource_type'],
                        'amount_needed' => $resource['amount_needed'],
                    ]);
                }
            }

            return $project;
        });
    }

    public function getProgressAttribute(): float
    {
        $totalNeeded = 0;
        $totalFound = 0;

        foreach ($this->phases as $phase) {
            foreach ($phase->resources as $resource) {
                $totalNeeded += (float) $resource->amount_needed;
                $totalFound += (float) ($resource->amount_found ?? 0);
            }
        }

        if ($totalNeeded <= 0) {
            return 0;
        }

        return round(($totalFound / $totalNeeded) * 100, 2);
    }

    public static function statusCounts()
    {
        return DB::table('projects')
            ->select('status')
            ->selectRaw('count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }
}
