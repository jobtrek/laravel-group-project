---
name: client-notes-densifier
description: Use this skill whenever Thomas has a folder of overlapping markdown notes (e.g. a /docs directory on a client project) that need to be condensed into one trustworthy summary before a client-side or team discussion. Trigger on phrases like "densify these notes", "check my docs for conflicting info", "clean up these notes before the client call", "some of these notes are wrong", "find where the team disagrees", or any request to consolidate scattered project documentation. The skill reads only markdown/text files — it deliberately skips MLD diagrams, PNGs, and other binary/image files since those aren't meant to be parsed as text. Whenever the same fact is stated with different values across files, the skill ALWAYS stops and writes a clarification/*.md file pinpointing exactly where the conflict lives, before producing anything else — it never silently guesses which value is correct. It then always produces a clean, densified summary doc, flagging any unresolved conflicts inline instead of papering over them. Trigger this even if Thomas doesn't use the word "densify" — "are these docs consistent" or "summarize /docs for the client" are the same task.
---

# Client Notes Densifier

## Why this exists

When a team writes notes independently over weeks, the same fact ends up stated in several places — sometimes worded differently, sometimes with a value that's quietly gone stale or was just wrong to begin with. Before a client conversation you need ONE clean fact sheet, but you can't get there by guessing which of two conflicting numbers is right — only a teammate can resolve that. This skill's job is to do the boring, exhaustive cross-referencing a human wouldn't have patience for, surface every disagreement loudly, and otherwise produce the densest, cleanest summary possible.

The core discipline: **never silently pick a winner between conflicting values.** Flag it, point at the exact files, and let the human resolve it.

## Step 1 — Scope the scan

Default to recursively scanning a `/docs` folder (or wherever the notes were uploaded) for `.md` / `.markdown` files. If Thomas points at a different folder or set of files, use that instead.

Skip entirely, without trying to read or interpret them:
- Images: `.png`, `.jpg`, `.jpeg`, `.svg`, `.webp`
- Diagram/model files: `.drawio`, `.mld`, or anything that's clearly an MLD/ERD export rather than prose

These are visual artifacts, not data to cross-reference. It's fine to note in passing that a diagram exists if a text file references it (e.g. "see schema.png"), but don't attempt to extract facts from the image itself.

## Step 2 — Extract atomic facts per file

Read every markdown file. For each one, pull out atomic, comparable facts — anything with a definite value that could plausibly be repeated or contradicted elsewhere:

- Field names, types, constraints
- Business rules and workflow steps ("a revision needs 2 director approvals")
- Statuses, deadlines, dates
- Prices, quotas, counts, other figures
- Names, roles, contact info
- Naming conventions, endpoint paths, fixed properties of "the project" / "the client" / "the data model"

For each fact, keep track of: the value, the source filename, and a short quote or line reference so you can point back to it precisely later. A sentence like "the client wants X" is a fact about a stated preference and worth tracking; a sentence like "I think we should maybe..." is an opinion, not a fact to cross-reference for duplication — use judgment rather than mechanically grabbing every sentence.

## Step 3 — Compare facts across files

Group facts by what they're actually about, even when phrased differently — "review deadline" and "due date for the director review" are the same fact in two outfits. For every fact mentioned in 2+ files, classify the group:

- **Consistent duplicate** — same value everywhere it's mentioned. Not a problem; worth a quiet note in the summary since multiple files independently agree.
- **Conflicting duplicate** — different values for the same fact across files. This is a confusion that needs a human.

## Step 4 — On any conflict: stop and write the clarification file first

The moment you find a conflicting duplicate, resist the pull to just pick the value that "sounds more recent" or "more official" — you don't have the authority to resolve it, only the team does. Before writing the summary doc:

1. Create a `clarification/` folder if it doesn't exist yet.
2. Write one markdown file there, e.g. `clarification/conflicts-<short-topic-or-date>.md`. If a previous run already created one for the same date/topic, append new conflicts to it rather than overwriting silently.
3. Use this structure for each conflict:

```markdown
## Conflict: <short name for the fact, e.g. "Director review deadline">

- **File A** (`docs/proposal-form.md`, near "the deadline for..."): says **March 15**
- **File B** (`docs/meeting-notes-week3.md`, near "revised timeline"): says **March 22**

**Why it matters:** <one line — why this fact needs to be right before the client call>
**Suggested next step:** Ask <whoever seems closest to the source, if inferable> to confirm.
```

List every conflict found this way before moving on — don't stop after the first one.

## Step 5 — Always produce the densified summary

Whether or not conflicts were found, always produce one consolidated, client-ready fact sheet. This is the actual deliverable — the clarification file is a side effect for the team, not the main output.

- One line or short bullet per fact. This is about density, not narrative — a fact sheet, not an essay.
- **Consistent facts:** state the value plainly. Optionally cite which files confirm it if that adds confidence (e.g. "confirmed in 3 notes").
- **Conflicting facts:** do NOT pick one. Mark clearly, e.g. `⚠️ UNRESOLVED (see clarification/conflicts-...md) — candidates: March 15 / March 22`, so the reader has context without needing to open the other file.
- Group facts under sensible headers (by topic, by project area) rather than dumping a flat list — this is what makes it "densified" rather than just "concatenated."

## Output locations

Write both outputs as real files so Thomas can open and share them:
- `clarification/<file>.md` — only created if at least one conflict was found.
- `densified-summary.md` (or a name reflecting the project, e.g. `<project>-summary.md`) — always created.

If working in this computer environment, save both under `/mnt/user-data/outputs/` (creating the `clarification` subfolder there too), then share them with `present_files`. Mention briefly in chat whether any conflicts were found, but don't restate the whole clarification file inline — that's what the file is for.