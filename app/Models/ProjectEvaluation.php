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
}
