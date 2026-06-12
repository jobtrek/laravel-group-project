<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    protected $fillable = [
        'title', 'description', 'budget_global', 'but', 'perimetre',
        'status', 'current_stage',
        'proposer_id', 'leader_id', 'recolte_manager_id',
    ];

    protected $casts = [
        'but' => 'array',
    ];

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members');
    }

    public function evaluation()
    {
        return $this->hasOne(ProjectEvaluation::class);
    }

    public function phases()
    {
        return $this->hasMany(ProjectPhase::class);
    }

    public static function createProposal(array $data, int $proposerId): self
    {
        return DB::transaction(function () use ($data, $proposerId) {
            $project = self::create([
                'title' => $data['titre'],
                'description' => $data['description'],
                'but' => $data['buts'],
                'perimetre' => $data['perimetre'] ?? null,
                'status' => 'proposition',
                'current_stage' => 'proposition',
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
}
