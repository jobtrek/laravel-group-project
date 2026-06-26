<?php

namespace App\Enums;

enum Stage: string
{
    case Propositions = 'propositions';
    case Review = 'review';
    case Recolte = 'recolte';
    case EnCours = 'en-cours';
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
            self::Propositions => ['submitted', 'modification'],
            self::Review => ['approved'],
            self::Recolte => ['collecting', 'ready'],
            self::EnCours => ['active'],
            self::Archive => ['archived', 'completed', 'refused'],
        };
    }
}
