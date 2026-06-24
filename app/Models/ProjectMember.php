<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectMember extends Pivot
{
    use HasFactory;

    protected $table = 'project_members';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'project_id',
    ];
}
