<?php

namespace App\Models;

use Database\Factories\ProjectEvaluationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectEvaluation extends Model
{
    /** @use HasFactory<ProjectEvaluationFactory> */
    use HasFactory;

    protected $fillable = ['portee', 'impact', 'confiance', 'effort', 'project_id'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getPorteeNormalizedAttribute(): float
    {
        return round(min(100, max(0, ($this->portee / 50) * 100)), 2);
    }

    public function getImpactNormalizedAttribute(): float
    {
        return round(min(100, max(0, ($this->impact / 5) * 100)), 2);
    }

    public function getEffortNormalizedAttribute(): float
    {
        return round(min(100, max(0, ($this->effort / 5) * 100)), 2);
    }

    public function getConfianceNormalizedAttribute(): float
    {
        return round(min(100, max(0, (float) $this->confiance)), 2);
    }
}
