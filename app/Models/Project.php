<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
