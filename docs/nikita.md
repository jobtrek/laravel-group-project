# Project Tracking System

## General Workflow

The system includes three main stages in the project lifecycle.

### Stage 1. Project Creation

Any employee can create a project by providing its description, objectives, requirements, and implementation details.

Once created, the project is automatically submitted for administrative review.

### Stage 2. Administrative Review

The administration can take one of the following actions:

* Approve the project.
* Request additional details or clarification.
* Reject the project. In this case, the project is automatically moved to the archive.

### Stage 3. Approved Projects

After administrative approval, the project moves to the **Approved Projects** category.

From there, two outcomes are possible:

* If sufficient funding is available, the project receives the status **In Progress**.
* If funding is not available, the project is moved to the **Funding Search** category.

---

# Roles and Permissions

## Regular User

Can:

1. Create projects.
2. Edit their own projects.
3. Provide additional information when requested by the administration.

## Administration

Can:

1. View all projects regardless of their status.
2. Approve projects.
3. Reject projects.
4. Request additional details or clarification for submitted projects.

## Finance Team

Can:

1. View all projects.
2. Update the funding status of approved projects that have not yet entered implementation due to budget constraints.

## Observer

Can:

1. View all projects.
2. Edit or delete approved projects.

## System Administrator

Has full access to all system functionality and permissions.

---

# Archiving and Activity Monitoring

A separate archive should be maintained for each project category:

* Projects under review.
* Approved projects without funding.
* Projects in progress.

## Projects Under Review

If no activity is recorded on a project:

* After 1 month: mark the project with an orange indicator.
* After 2 months: mark the project with a red indicator.
* After 3 months: automatically move the project to the archive.

## Projects Awaiting Funding

The same activity monitoring process applies, but with longer time periods, which should be defined separately.

---

# Progress Updates for Active Projects

The project owner or project lead must provide a status update at least once per month.

If no update is provided:

1. After one month, an automatic reminder email is sent requesting a project status update.
2. If no update is submitted within one week after the reminder, a second, higher-priority notification is sent.
3. The second notification should include the employee’s manager or relevant leadership in CC.

---

# Recommended Project Statuses

To simplify workflow management, the following project statuses are recommended:

**Draft → Under Review → Requires Clarification → Approved → Funding Search → In Progress → Completed → Archived**

