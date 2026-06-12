<?php

namespace App\Models;

use app\Models\Project;
use app\Models\User;
use Database\Factories\ProjectReviewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project_review extends Model
{
    /** @use HasFactory<ProjectReviewFactory> */
    use HasFactory;

    protected $fillable = ['project_id', 'user_id', 'review_status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
