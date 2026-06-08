---
name: following-git-workflow
description: Guides developers through the team git workflow for this Laravel group project — conventional commits, branch naming, PR descriptions, and resolving merge conflicts. Use when the user asks about git commands, commits, branches, pull requests, merging, rebasing, or how to submit work for review.
---

# Git Workflow — Group Project

**This project uses Conventional Commits.** All commits and PR titles follow `type: description`.

## Branch naming

`type/short-description` — e.g. `feat/user-auth`, `fix/login-redirect`, `ci/test-pipeline`

## Commit types

| Type | When |
|------|------|
| `feat` | New feature |
| `fix` | Bug fix |
| `docs` | Documentation only |
| `refactor` | Restructure, no behaviour change |
| `test` | Tests |
| `chore` | Dependencies, config, tooling |
| `ci` | CI/CD pipeline changes |

See [references/examples.md](references/examples.md) for concrete good/bad examples.

## Daily workflow

```bash
git checkout main && git pull           # always start from latest main
git checkout -b feat/my-feature         # new branch for every piece of work
# ... make changes, commit often ...
git push -u origin feat/my-feature      # push and open a PR
```

## Commit messages

- Subject line under 72 characters
- Use present tense: "add login form" not "added login form"
- Don't end with a period

```bash
git commit -m "feat: add login form with email and password validation"
```

## Pull requests

Before opening:
1. `git rebase origin/main` — keep your branch up to date
2. Confirm the CI pipeline passes after pushing

PR title: same `type: description` format as commits.
PR body: what changed, why, and how to test it.

## Resolving merge conflicts

1. `git fetch origin && git rebase origin/main`
2. Fix `<<<<<<<` / `=======` / `>>>>>>>` markers in conflicted files
3. `git add <resolved-file>` then `git rebase --continue`
4. `git push --force-with-lease origin your-branch` — safe force push (fails if someone else pushed)

## Rules

- Never commit directly to `main`
- Never `--force` push `main`
- Rebase your own branch to keep it current; GitHub merges it when the PR closes
- Don't rebase a branch others are also working on
