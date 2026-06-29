<?php

namespace App\Enums;

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
            self::Evaluation => ['évaluation'],
            self::Recolte => ['récolte'],
            self::EnCours => ['En cours'],
            self::Archive => ['archivé', 'complété'],
        };
    }
}
