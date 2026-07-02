<?php

namespace App\Enums;

enum Stage: string
{
    case Propositions = 'propositions';
    case Evaluation = 'evaluation';
    case Recolte = 'recolte';
    case EnCours = 'en-cours';
    case Complete = 'complete';
    case Archive = 'frigo';

    public function title(): string
    {
        return match ($this) {
            self::Propositions => 'Propositions',
            self::Evaluation => 'Evaluation',
            self::Recolte => 'Récolte',
            self::EnCours => 'En cours',
            self::Complete => 'Complété',
            self::Archive => 'Frigo',
        };
    }

    public function prev(): ?self
    {
        return match ($this) {
            self::Evaluation => self::Propositions,
            self::Recolte => self::Evaluation,
            self::EnCours => self::Recolte,
            self::Complete => self::EnCours,
            self::Archive => self::Complete,
            default => null,
        };
    }

    public function next(): ?self
    {
        return match ($this) {
            self::Propositions => self::Evaluation,
            self::Evaluation => self::Recolte,
            self::Recolte => self::EnCours,
            self::EnCours => self::Complete,
            self::Complete => self::Archive,
            default => null,
        };
    }

    public function statuses(): array
    {
        return match ($this) {
            self::Propositions => ['proposition', 'révision'],
            self::Evaluation => ['évaluation'],
            self::Recolte => ['récolte'],
            self::EnCours => ['en cours'],
            self::Complete => ['complété'],
            self::Archive => ['archivé'],
        };
    }
}
