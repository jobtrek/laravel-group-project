<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectPhase extends Model
{
    use HasFactory;

    protected $table = 'project_phases';

    public $timestamps = false;

    protected $fillable = [
        'title',
        'duration',
        'description',
        'objectifs',
        'livrables',
        'order',
        'project_id',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }


    public function resources(): HasMany
    {
        return $this->hasMany(PhaseResource::class);
    }
}