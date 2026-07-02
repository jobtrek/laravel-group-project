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
        return $this->normalize($this->portee, 50);
    }

    public function getImpactNormalizedAttribute(): float
    {
        return $this->normalize($this->impact, 5);
    }

    public function getEffortNormalizedAttribute(): float
    {
        return $this->normalize($this->effort, 5);
    }

    public function getConfianceNormalizedAttribute(): float
    {
        return $this->normalize($this->confiance);
    }

    protected function normalize(mixed $value, float $max = 100.0): float
    {
        return round(min(100.0, max(0.0, ((float) $value / $max) * 100.0)), 2);
    }
}
