---
name: fix-simple-issues-implementer
description: >
  Implements or fixes a specific, well-scoped GitHub issue in an isolated git
  worktree, validates it against the project's formatting/static-analysis
  gates, and leaves a log for human review. Trigger whenever the person asks
  to fix, implement, or pick up a specific issue by number (e.g. "fix issue
  #42", "implement issue 17", "can you handle issue #8"), or when an issue
  is tagged/labeled "Agent". Do not commit or open a PR — that stays with
  the human.
---

## Purpose

Pick up one GitHub issue, implement it in isolation, and hand it back ready
for human review — without touching git history or opening a PR yourself.
The human reviews the diff, decides whether to commit, and owns the PR.

## Workflow

### 1. Set up an isolated worktree

Create a git worktree per issue rather than switching branches in the main
checkout, so several issues can be worked on in parallel without stepping on
each other or on the person's own in-progress work:

```bash
git worktree add ../<project>-issue-<N> -b fix/issue-<N>
```

Inside the worktree, get the app into a runnable state so you can actually
verify the fix, not just read the code:

```bash
./vendor/bin/sail up -d
npm run dev   # in a separate terminal
```

### 2. Implement the fix

Keep the change scoped to what the issue describes. If something is
genuinely ambiguous — acceptance criteria, expected behavior, which of two
reasonable approaches to take — use the `grill` skill to interview the
person until it's resolved, rather than guessing. Likewise, if partway
through you find the issue is bigger than it looked (it touches areas
clearly outside its stated scope), pause and grill the person on whether to
expand scope, split the issue, or stop where you are.

### 3. Validate before calling it done

The fix isn't finished until it passes both:

```bash
./vendor/bin/pint          # code style
./vendor/bin/phpstan analyse  # static analysis
```

Both must be clean. A fix that "works" but fails formatting or static
analysis just creates cleanup work for the human reviewer later — treat
these as part of the implementation, not an optional last step.

### 4. Log the change

Write a log file to `/docs/logs/`, numbered sequentially: check what's
already in that directory and use the next number (`ISSUELOG_1.md`,
`ISSUELOG_2.md`, ...).

The log exists so a reviewer can understand your reasoning from the diff
alone, without re-deriving it. Keep each one short but complete:

```markdown
# Issue #<N>: <short title>

## What changed
- <file>: <one-line summary of the change>
- <file>: <one-line summary of the change>

## Why
<Why this approach — the reasoning a reviewer would otherwise have to guess at.>
```

### 5. Stop — do not commit

Leave the working tree as-is with the changes unstaged/uncommitted. Do not
run `git commit`, and do not open a PR. The person reviews the diff
themselves and decides what happens next; committing on their behalf would
skip that review.