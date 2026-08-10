# App Suivis Projets — Notion Notes

*Extracted from `docs/notion_notes/app suivis projets 3795f13d7e4e803ca942e3a3c9ba36be.pdf` (20-page Notion export). Raw team notes — not authoritative, superseded by `docs/Source_of_truth.md` where they conflict. Kept here for traceability alongside the other primary sources in `raw_docs/`.*

---

## Rappel (working process)

- Take all notes here (meeting and non-meeting).
- Write down every question as soon as it comes to mind.
- Regularly re-read the file to:
  - Group related information together.
  - Fill in answers to open questions.
  - Create new questions about points that need more precision.

**Before a client meeting:**
- Re-read the file carefully.
- Get clear on the questions to ask and the features to demo.
- Send the client an email announcing the features to be presented and/or the questions to be asked.
- Run `sail up` and verify the app runs without bugs for the demo.
- Think through the classic "traps" (questions) the client might raise and be ready to answer/justify.

---

## 1. Contexte général

Cette section reste fixe et sert de référence pour ne pas oublier le "Pourquoi".

**Problème :** Proposer un projet, le faire valider, suivre la phase de préparation et d'exécution sont des tâches relativement coûteuses administrativement. On veut pouvoir avoir une vue d'ensemble sur l'ensemble du processus de suivi d'un projet sur une application, où l'on doit pouvoir naviguer de manière fluide et directe.

**Objectif :** Trier les propositions de projet, traquer et voir leur avancement. On souhaite pouvoir gérer les projets de manière centralisée sans traquer chaque tâche du projet. On veut :
- Permettre aux collaborateurs de facilement créer et transmettre leurs projets à la direction.
- Permettre à la direction de facilement valider, archiver et mettre en suspens pour validation un projet en validation.
- Pouvoir archiver et garder les projets et les reprendre depuis les archives.
- Permettre aux collaborateurs de suivre la préparation de projet et la collecte de ressources.
- Avoir une vue d'ensemble sur les projets : en validation / en financement / en préparation / en cours de réalisation.

> L'application ne doit pas contenir de todo list ou de liste de tâches quelconques — c'est une tâche extérieure. Elle doit se concentrer sur la gestion de projet en vue générale. La notion de vue d'ensemble semble être la priorité du client.

**Cibles :** La direction (pour le suivi de projet) principalement, ainsi que les collaborateurs pour la soumission et pour être tenus au courant de ce que devient leur proposition de projet.

---

## 2. Backlog de précisions (Q&A avec le client)

*Statuts : **Validé** = confirmé avec le client · **A clarifier** = réponse partielle/à affiner · **A poser** = pas encore posée*

### Formulaire / évaluation

| Question | Statut | Urgence | Réponse |
|---|---|---|---|
| Pouvez-vous nous réexpliquer ce qu'est le périmètre d'un projet selon vous ? | Validé | Urgent | L'utilisateur doit saisir textuellement ce qui est inclus et ce qui est exclu du projet. |
| Qu'est-ce que la taille d'un projet selon vous ? Comment la définir ? | Validé | Urgent | L'application doit permettre d'évaluer la pertinence d'un projet via un système de notation par points : Impact (1 à 5, combien de personnes touche ce projet), Effort/Ressources (évaluation du besoin humain, le temps que ça va prendre : 1 mois, 2 mois, etc.). Référence : formule RICE. |
| Que sont les bénéficiaires et les partenaires ? | A clarifier | Modérée | — |
| Pouvez-vous nous expliquer la formule d'impact ? | Validé | Urgent | Reçue par mail. |
| Éclaircir les sections suivantes : Périmètres / Définition de taille du projet / Évaluation avec les différents points / Dans la partie Portée, qui sont les partenaires et les bénéficiaires ? | A clarifier | Urgent | Périmètre : ce qu'un projet doit et ne doit pas faire. Le reste concerne la formule d'impact et a été clarifié avec le client — ce sera le collaborateur qui le gérera. Les doutes restants sont mis de côté pour l'instant. |
| Comment gérer les phases de projet au moment de la création du projet ? | Validé | Urgent | Le client veut qu'on murisse la réflexion du projet à la création. C'est le créateur de la proposition qui entre les phases du projet. |
| Comment afficher l'impact ? Échelle 1–5 ? Score RICE brut ? Mots-clés ("Faible/Moyen/Haut-moyen/Fort") ? | Validé | Urgent | Uniquement un chiffre (résultat de la formule) — design uniquement. |
| Souhaitez-vous afficher différemment le score RICE selon sa hauteur ? *(question design, à repousser)* | A poser | Faible | — |
| Si un projet est reconduit après review, le collaborateur re-remplit un nouveau formulaire ou a accès à un onglet de correction ? | A poser | Faible | — |

### Base de données / rôles

| Question | Statut | Urgence | Réponse |
|---|---|---|---|
| Pouvez-vous clarifier les rôles au sein de votre entreprise ? | Validé | Urgent | Les rôles, ce sont les droits et ce que font les collaborateurs. |
| Pour les phases d'un projet : qui définit les phases — les collaborateurs ou la direction ? | Validé | Urgent | Les phases d'un projet, ce sont les objectifs atteints. La personne qui propose le projet doit fournir ces phases. |
| Pour l'impact d'un projet : qui définit les critères d'impact ? Le budget ? Le temps ETP ? Que veut dire ETP ? | Validé | Modérée | La personne qui a créé le projet doit fournir toutes ces informations, et la Direction doit l'évaluer. |
| Que deviennent les projets en cours abandonnés / reconduits / achevés ? | A poser | Faible | — |
| Quels rôles souhaitez-vous avoir dans l'application, et que peut faire chacun (responsabilités, visibilité, droits de modification, attribution des rôles) ? | A clarifier | Urgent | **ADMIN** : tous les droits absolus sur l'application. **Chef de projet** : peut demander et publier des mises à jour régulières pour que les infos du projet restent à jour. **Support** : recherche des ressources disponibles pour le projet. **User** : consulte toutes les infos de l'app mais ne peut modifier que les projets qu'il a lui-même démarrés. **Gestionnaire de projet** : rôle non précisé à ce stade. **Direction** : responsable de la validation globale des projets. |
| Est-ce qu'un membre de la direction peut soumettre une proposition de projet ? Si oui, comment est-elle jugée ? | Validé | Urgent | Tout le monde peut proposer un projet ; validée comme les autres. Un membre de la direction ne peut pas se valider tout seul — acceptation requise par tous les membres de la direction. |
| Les collaborateurs créent-ils leur propre compte, ou est-ce la direction/l'entreprise qui les crée ? | Validé | Urgent | Ils peuvent créer leur propre compte, mais c'est l'ADMIN qui leur attribue les rôles. |
| Peut-on avoir plusieurs chefs de projet sur un même projet ? | Validé | Urgent | Non. |
| Comment souhaitez-vous saisir les participants à un projet et le chef de projet ? | Validé | Urgent | Saisie individuelle. |
| Quand est-ce qu'un chef de projet est nommé ? | A clarifier | Urgent | C'est la direction qui le nomme à la validation — étape précise non confirmée. |
| Comment visualiser les collaborateurs assignés à un projet (équipe / utilisateurs uniques / uniquement le chef de projet) ? | Validé | Urgent | Liste de participants. |
| Comment gérer les échéances/statuts des projets à travers les phases (récolte, en cours) ? | A poser | Faible | — |
| Remise au clair sur la gestion des projets en cours de proposition et sur la circulation à travers le flux de travail (question basée sur le MCD) | A clarifier | Faible | On y répondra au fur et à mesure, en fonction du reste. |
| Est-ce qu'on veut un historique de projet ? | A clarifier | Faible | Normalement oui — à préciser pour les projets exécutés, car on veut savoir ce qu'ils deviennent. |
| Comment voulez-vous traquer les phases de vos projets ? | A poser | Faible | — |
| Dans la récolte, seuls les responsables du support peuvent-ils mettre à jour les informations de collecte de fonds ? | A poser | Faible | — *(catégorie : Permissions)* |
| Qui gère les projets en archive ? | A poser | Faible | — *(catégorie : Permissions)* |
| Quel est le rôle du gestionnaire de projet ? | A clarifier | Faible | Apparemment il peut modifier des projets en cours, y compris ceux qui ne sont pas les siens — à reconfirmer absolument avec le client. *(catégorie : Permissions)* |

### Archives / frigo

| Question | Statut | Urgence | Réponse |
|---|---|---|---|
| Qui a accès aux frigos et aux archives ? | Validé | Urgent | Tout le monde. |
| Conserve-t-on les propositions de projet telles quelles dans le Frigo (description, etc.) ? | A poser | Faible | — |
| Pour les projets archivés, combien de temps les garde-t-on avant suppression définitive ? | A clarifier | Faible | 1 an — mais le client a mentionné plusieurs types d'archives, à revoir avec lui pour être sûr. |
| Comment gérez-vous les vieilles archives, combien de temps les gardez-vous ? | A poser | Modérée | — |
| Que faites-vous des projets archivés que vous réutilisez plus tard — les remettez-vous au stade où ils étaient ? | A poser | Modérée | — |
| Où afficher les projets complétés ? | A poser | Faible | Dans les archives pendant 1 an, puis suppression définitive. |
| Est-ce que les projets qui passent en archive depuis la récolte (12 mois sans changement) transfèrent leurs ressources trouvées vers un autre projet ? | A poser | — | — |
| Si un projet est archivé depuis la récolte, qui reçoit le mail ? | A poser | — | — |
| Les projets finis passent-ils en archive, ou fait-on un nouveau dashboard "complété" (et aussi pour la révision) ? | A poser | — | — |

### Affichage / dashboard

| Question | Statut | Urgence | Réponse |
|---|---|---|---|
| Sépare-t-on l'affichage par page selon le statut du projet, ou tout sur une seule page filtrée par statut ? | A poser | Urgent | — |
| Quelles infos mettre à disposition des collaborateurs dans le dashboard (vue générale) ? | Validé | Urgent | — *(réponse non détaillée dans le doc)* |
| Pour les dashboards proposition et frigo, sépare-t-on les 4 statuts en 4 dashboards distincts ? | A poser | Modérée | Le frigo est séparé des complétés. |
| Dans la modification de projet, peut-on tout modifier, à n'importe quel stade ? | A poser | Urgent | Dès que le projet n'est plus en proposition, il n'est plus modifiable. |
| Que fait-on si on trouve plus d'argent que nécessaire (`$totalFound / $totalNeeded * 100 > 100`) ? | A poser | Urgent | Barre qui dépasse les 100% avec une marque visible. |
| À quel moment décide-t-on qui est le project leader ? | A poser | — | — |

---

## 3. Meetings

### Meeting du 19.06 — Clarifier certains points
*Participants : Joao, Mykyta*

- Ressources : `somme à atteindre - somme récoltée = pourcentage` ; tout est exprimé en argent ; taux de travail = salaire.
- Modifiable par le gestionnaire, la direction, et toute personne dans la récolte.
- Communication App → User : par email.
- Projets en cours : afficher clairement la date de dernière modification.
- Attribution du chef de projet par le gestionnaire de projet, ou (plus rarement) par la direction.
- Couleur rouge si un projet n'a pas eu de mise à jour pendant 2–3 mois (peu importe le statut) — mais le calcul diffère selon le statut.
- Un projet archivé qu'on reprend redevient une **nouvelle proposition** (status: submitted) → `current_stage` à supprimer de la DB.
- La formule d'importance est figée dans le temps (pas besoin de la recalculer au niveau DB).

### Sprint planning du 16.06
- Améliorer l'UI.
- Faire fonctionner la direction.
- Gérer complètement l'affichage des projets.

### Sprint review du 12.06
*(pas de notes détaillées)*

### Meeting du 08.06 — Clarifier certains points (notamment rôles, formulaire…)
*Participants : Ryan, Thomas*

### Sprint planning du 05.06
*(pas de notes détaillées)*

### Meeting du 05.06 — Premier meeting, découverte de la demande client
*Participants : Tiziano, Igor*

### Meeting du 02.07
*Participants : Igor, Ryan, Joao*

- Remove the members field — you can't choose members while doing a proposition.
- Display CHF in the formulaire.
- Careful with the importance calculation, especially with the percentage.
- The chef de projet should be the one doing the evaluation — just update the naming.
- There should be a button to move a project into "en cours".
- If more resources are found than necessary → increase the bar size and show the overflow past 100%.
- Everything can be modified before proposition; after proposition, nothing can be modified.
- Separate finished projects from archived projects.
- Chef de projet receives the mail, [as well as] the coordinateur and direction.
- Chef de projet: during the récolte phase, choose the chef de projet and members, then a button to move to "en cours".
- Filter by default → activate filtering automatically when someone connects.

---

## 4. Fonctionnement de l'app & décisions

> Cette section centralise ce que l'application doit faire — toutes les informations obtenues en discutant avec le client. Elle est mise à jour dès qu'une question est validée.

### Sprints — Product Owner / Scrum Master

| Sprints | Product Owner | Scrum Master |
|---|---|---|
| 1–2 | Igor | Tiziano |
| 3–4 | Ryan | Thomas |
| 5–6 | Mykyta | Joao |

---

## 5. Choix techniques & justifications

> Cette section sert d'argumentaire lors des revues de projet ou des démos. *(Vide dans ce document — pas encore rempli au moment de l'export.)*

---

## Code à se partager

Guidelines from the team notes page:
- Respect the marked section — do not put code outside of it.
- Warning: track changes not covered by git (e.g. `.env`, migrations, etc.) — document them properly, otherwise things get lost.
- Explain what the code does, don't just paste it.
- Once a code snippet is no longer useful (already implemented, etc.), **delete it**.
- Be careful with AI usage.

**Default test user:**
- Name: `John Doe`
- Email: `john@example.com`
- Password: `password`

---

*Pages 17–20 of the source PDF contained no extractable text (blank/footer-only pages in the Notion export).*
