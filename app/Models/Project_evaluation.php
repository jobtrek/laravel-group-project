<?php

namespace App\Models;

use app\Models\Project;
use app\Models\User;
use Database\Factories\ProjectEvaluationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class project_evaluation extends Model
{
    /** @use HasFactory<ProjectEvaluationFactory> */
    use HasFactory;

    protected $fillable = ['portee', 'impact', 'confiance', 'effort', 'importance', 'project_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
