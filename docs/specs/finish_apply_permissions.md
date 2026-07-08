# Finish permissions

Okay so recently my colleague has implement the declaration of the roles and permissions of the users inside **database/seeders/RoleAndPermissionSeeder.php**, however what I want you to do is to analyse the frontend, and then apply the @can / endcan directives, basing off

## Roles

- **Multiple roles per person are allowed** (a user can be collaborateur + direction, etc.).
- Role assignment: **admin only**.
- Default role on account creation: **collaborateur**.

| Role | Key permissions |
|------|----------------|
| **Collaborateur** | Propose projects; edit own proposals | he can see all projects
| **Direction** | Approve / refuse / suspend proposals; comment in Direction module; see all | | he can also archive projects | and evaluate projects
| **Récolte Manager** | Add/update resources on Récolte projects; see all |
| **Project manager** | he can't evaluate, | but he can archive project. |. 
| **Chef de projet** | Comment on all En cours projects; launch projects from Récolte; mark complete; see all |
| **Admin** | Full access; only role that can assign/change roles |

### confusion
if you are confused, you may start a grill session with me to ask clarification question but first analyse the frontend repo and some backend to confirm that there all everything there.

