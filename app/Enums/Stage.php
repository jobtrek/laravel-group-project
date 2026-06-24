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
            self::Review => 'Review',
            self::Recolte => 'Récolte',
            self::EnCours => 'En cours',
            self::Archive => 'Archive',
        };
    }

    public function prev(): ?array
    {
        return match ($this) {
            self::Review => ['route' => 'propositions', 'label' => 'Propositions'],
            self::Recolte => ['route' => 'review', 'label' => 'Review'],
            self::EnCours => ['route' => 'recolte', 'label' => 'Récolte'],
            self::Archive => ['route' => 'en-cours', 'label' => 'En cours'],
            default => null,
        };
    }

    public function next(): ?array
    {
        return match ($this) {
            self::Propositions => ['route' => 'review', 'label' => 'Review'],
            self::Review => ['route' => 'recolte', 'label' => 'Récolte'],
            self::Recolte => ['route' => 'en-cours', 'label' => 'En cours'],
            self::EnCours => ['route' => 'archive', 'label' => 'Archive'],
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
