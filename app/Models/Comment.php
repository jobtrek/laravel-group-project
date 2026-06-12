<?php

namespace App\Models;

use app\Models\User;
use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory;

    protected $fillable = ['content', 'stage', 'user_id', 'project_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
