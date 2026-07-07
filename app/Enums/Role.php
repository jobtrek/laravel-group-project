<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Direction = 'direction';
    case ChefDeProjet = 'chef_de_projet';
    case ProjectManager = 'project_manager';
    case RecolteManager = 'recolte_manager';
    case Collaborateur = 'collaborateur';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrateur',
            self::Direction => 'Direction',
            self::ChefDeProjet => 'Chef de projet',
            self::ProjectManager => 'Gestionnaire de projets',
            self::RecolteManager => 'Support ressources',
            self::Collaborateur => 'Collaborateur',
        };
    }
}
