<?php

namespace App\Models\States;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class ProjectState extends State
{
    abstract public function label(): string;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(PropositionState::class)
            ->allowTransitions([
                [PropositionState::class, EvaluationState::class],

                [EvaluationState::class, RevisionState::class],
                [EvaluationState::class, ArchiveState::class],
                [EvaluationState::class, RecolteState::class],

                [RevisionState::class, PropositionState::class],

                [RecolteState::class, EncoursState::class],
                [RecolteState::class, ArchiveState::class],

                [EncoursState::class, CompleteState::class],
                [EncoursState::class, ArchiveState::class],

                [ArchiveState::class, PropositionState::class],
            ]);
    }

    public function isEditable(): bool
    {
        return false;
    }

}
