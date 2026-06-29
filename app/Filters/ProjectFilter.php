<?php

namespace App\Filters;

use App\Enums\ProjectSort;
use App\Http\Requests\FilterProjectsRequest;
use Illuminate\Database\Eloquent\Builder;

class ProjectFilter
{
    public function apply(Builder $query, FilterProjectsRequest $request): Builder
    {
        return $query
            ->when(
                $request->filled('score_min'),
                fn (Builder $q) => $q->whereRelation(
                    'evaluation', 'importance', '>=', $request->integer('score_min')
                )
            )
            ->when(
                $request->filled('date_from'),
                fn (Builder $q) => $q->whereDate('projects.created_at', '>=', $request->input('date_from'))
            )
            ->when(
                $request->filled('date_to'),
                fn (Builder $q) => $q->whereDate('projects.created_at', '<=', $request->input('date_to'))
            )
            ->when(
                $request->filled('proposer_id'),
                fn (Builder $q) => $q->where('proposer_id', $request->integer('proposer_id'))
            )
            ->when(
                $request->filled('sort'),

                fn (Builder $q) => ProjectSort::from($request->input('sort'))->apply($q)
            )
            ->when(
                ! $request->filled('sort'),
                fn (Builder $q) => $q->orderBy('projects.created_at', 'desc')
            );
    }
}
