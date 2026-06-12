<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use app\Models\User;
use app\Models\Project;

class project_evaluation extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectEvaluationFactory> */
    use HasFactory;
    private $fillable = ['portee', 'impact', 'confiance', 'effort', 'importance', 'project_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
