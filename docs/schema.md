# Schémas

Ce document va repertorier les schémas utile à la compréhension de la demande client. Il contien(dra) différent schéma de différent 'flow', ou autre. 

## User flow

La navigation d'un projet proposé par un collaborateur selon client.

```mermaid
flowchart LR
    A[Collaborateur remplis un formulaire] --> B[Direction reçois] --> C{Projet validé ?}
    C -- Oui --> D[On l'envoie en récolte] --> G[On attend d'avoir 80% des ressources nécéssaires] --> H[On envoie le projet en production.]
    C -- Non --> E[On l'archive dans le frigo]
    C -- Suspendu / Review --> F[On le renvoie en modification à l'utilisateur] --> A
```

## Project request flow

```mermaid
flowchart LR
    A[Collaborateur propose projet via formulaire] 
```