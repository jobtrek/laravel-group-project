<?php

namespace App\Enums;

use App\Models\States\ActiveState;
use App\Models\States\ApprovedState;
use App\Models\States\ArchivedState;
use App\Models\States\CollectingState;
use App\Models\States\CompletedState;
use App\Models\States\ModificationState;
use App\Models\States\ReadyState;
use App\Models\States\RefusedState;
use App\Models\States\SubmittedState;

enum Stage: string
{
    case Propositions = 'propositions';
    case Evaluation = 'evaluation';
    case Recolte = 'recolte';
    case EnCours = 'en-cours';
    case Archive = 'frigo';

    public function title(): string
    {
        return match ($this) {
            self::Propositions => 'Propositions',
            self::Evaluation => 'Evaluation',
            self::Recolte => 'Récolte',
            self::EnCours => 'En cours',
            self::Archive => 'Frigo',
        };
    }

    public function prev(): ?self
    {
        return match ($this) {
            self::Evaluation => self::Propositions,
            self::Recolte => self::Evaluation,
            self::EnCours => self::Recolte,
            self::Archive => self::EnCours,
            default => null,
        };
    }

    public function next(): ?self
    {
        return match ($this) {
            self::Propositions => self::Evaluation,
            self::Evaluation => self::Recolte,
            self::Recolte => self::EnCours,
            self::EnCours => self::Archive,
            default => null,
        };
    }

    public function statuses(): array
    {
        return match ($this) {
            self::Propositions => ['proposition', 'révision'],
            self::Evaluation => ['evaluation'],
            self::Recolte => ['récolte'],
            self::EnCours => ['en cours'],
            self::Archive => ['archivé', 'complété'],
        };
    }
}
