<?php

namespace App\Enums;

use Illuminate\Database\Eloquent\Builder;

enum ProjectSort: string
{
    case AZ = 'az';
    case ZA = 'za';
    case Recent = 'recent';
    case Oldest = 'oldest';
    case ImportanceDesc = 'importance_desc';
    case ImportanceAsc = 'importance_asc';

    public function apply(Builder $query): Builder
    {
        return match ($this) {
            self::AZ => $query->orderBy('projects.title', 'asc'),
            self::ZA => $query->orderBy('projects.title', 'desc'),
            self::Recent => $query->orderBy('projects.created_at', 'desc'),
            self::Oldest => $query->orderBy('projects.created_at', 'asc'),
            self::ImportanceDesc => $query
                ->leftJoin('project_evaluations', 'project_evaluations.project_id', '=', 'projects.id')
                ->orderBy('project_evaluations.importance', 'desc')
                ->select('projects.*'),

            self::ImportanceAsc => $query
                ->leftJoin('project_evaluations', 'project_evaluations.project_id', '=', 'projects.id')
                ->orderBy('project_evaluations.importance', 'asc')
                ->select('projects.*'),
        };
    }
}
