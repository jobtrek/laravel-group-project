<?php

namespace App\Models\States;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class ProjectState extends State {
    abstract public function label(): string;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(SubmittedState::class)
            ->allowTransitions([
                // From Submitted
                [SubmittedState::class, ApprovedState::class],
                [SubmittedState::class, RefusedState::class],
                [SubmittedState::class, ModificationState::class],
                [SubmittedState::class, ArchivedState::class],

                // From Modification
                [ModificationState::class, SubmittedState::class],
                [ModificationState::class, ArchivedState::class],

                // From Approved
                [ApprovedState::class, CollectingState::class],

                // From Refused
                [RefusedState::class, SubmittedState::class],

                // From Collecting
                [CollectingState::class, ReadyState::class],
                [CollectingState::class, ArchivedState::class],

                // From Ready
                [ReadyState::class, ActiveState::class],
                [ReadyState::class, CollectingState::class],
                [ReadyState::class, ArchivedState::class],

                // From Active
                [ActiveState::class, CompletedState::class],
                [ActiveState::class, ArchivedState::class],

                // From Archived
                [ArchivedState::class, SubmittedState::class],
                [ArchivedState::class, CollectingState::class],
                [ArchivedState::class, ActiveState::class],
            ]);
    }
}
