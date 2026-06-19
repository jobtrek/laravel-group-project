<?php

namespace App\Models;

use Database\Factories\ProjectReviewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectReview extends Model
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
