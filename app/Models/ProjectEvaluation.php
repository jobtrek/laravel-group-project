<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectEvaluation extends Model
{
    protected $fillable = [
        'portee', 'impact', 'confiance', 'effort', 'project_id',
    ];
}
