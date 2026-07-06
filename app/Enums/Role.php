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
}
