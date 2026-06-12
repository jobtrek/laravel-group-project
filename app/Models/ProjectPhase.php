<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPhase extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name', 'duration', 'objectifs', 'livrables', 'order', 'project_id',
    ];

    protected $casts = [
        'objectifs' => 'array',
        'livrables' => 'array',
    ];

    public function resources()
    {
        return $this->hasMany(PhaseResource::class, 'phase_id');
    }
}
