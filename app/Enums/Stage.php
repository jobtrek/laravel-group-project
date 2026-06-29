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
    case Review = 'review';
    case Recolte = 'recolte';
    case EnCours = 'enCours';
    case Archive = 'archive';

    public function title(): string
    {
        return match ($this) {
            self::Propositions => 'Propositions',
            self::Review => 'Evaluation',
            self::Recolte => 'Récolte',
            self::EnCours => 'En cours',
            self::Archive => 'Frigo',
        };
    }

    public function prev(): ?self
    {
        return match ($this) {
            self::Review => self::Propositions,
            self::Recolte => self::Review,
            self::EnCours => self::Recolte,
            self::Archive => self::EnCours,
            default => null,
        };
    }

    public function next(): ?self
    {
        return match ($this) {
            self::Propositions => self::Review,
            self::Review => self::Recolte,
            self::Recolte => self::EnCours,
            self::EnCours => self::Archive,
            default => null,
        };
    }

    public function statuses(): array
    {
        return match ($this) {
            self::Propositions => [SubmittedState::class, ModificationState::class],
            self::Review => [ApprovedState::class],
            self::Recolte => [CollectingState::class, ReadyState::class],
            self::EnCours => [ActiveState::class],
            self::Archive => [ArchivedState::class, CompletedState::class, RefusedState::class],
        };
    }
}
