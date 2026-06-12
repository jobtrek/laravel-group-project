<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use app\Models\User;
use app\Models\Project;

class Project_review extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectReviewFactory> */
    use HasFactory;
    private $fillable = ['project_id', 'user_id', 'review_status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
