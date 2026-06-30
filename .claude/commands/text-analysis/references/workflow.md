# Client Notes Densifier — Detailed Workflow

## Step 1 — Scope the scan

Default to recursively scanning a `/docs` folder (or wherever the notes were uploaded) for `.md` / `.markdown` files. If Thomas points at a different folder or set of files, use that instead.

Skip entirely, without trying to read or interpret them:
- Images: `.png`, `.jpg`, `.jpeg`, `.svg`, `.webp`
- Diagram/model files: `.drawio`, `.mld`, or anything that is clearly an MLD/ERD export rather than prose

These are visual artifacts, not data to cross-reference. It is fine to note in passing that a diagram exists if a text file references it (e.g. "see schema.png"), but do not attempt to extract facts from the image itself.

## Step 2 — Extract atomic facts per file

Read every markdown file. For each one, pull out atomic, comparable facts — anything with a definite value that could plausibly be repeated or contradicted elsewhere:

- Field names, types, constraints
- Business rules and workflow steps ("a revision needs 2 director approvals")
- Statuses, deadlines, dates
- Prices, quotas, counts, other figures
- Names, roles, contact info
- Naming conventions, endpoint paths, fixed properties of "the project" / "the client" / "the data model"

For each fact, keep track of: the value, the source filename, and a short quote or line reference so you can point back to it precisely later. A sentence like "the client wants X" is a fact about a stated preference and worth tracking; a sentence like "I think we should maybe..." is an opinion, not a fact to cross-reference.

## Step 3 — Compare facts across files

Group facts by what they are actually about, even when phrased differently — "review deadline" and "due date for the director review" are the same fact in two outfits. For every fact mentioned in 2+ files, classify the group:

- **Consistent duplicate** — same value everywhere it is mentioned. Not a problem; worth a quiet note in the summary since multiple files independently agree.
- **Conflicting duplicate** — different values for the same fact across files. This needs a human to resolve.

## Step 4 — On any conflict: stop and write the clarification file first

The moment you find a conflicting duplicate, resist the pull to just pick the value that "sounds more recent" or "more official" — you do not have the authority to resolve it, only the team does. Before writing the summary doc:

1. Create a `clarification/` folder if it does not exist yet.
2. Write one markdown file there, e.g. `clarification/conflicts-<short-topic-or-date>.md`. If a previous run already created one for the same date/topic, append new conflicts to it rather than overwriting silently.
3. Use this structure for each conflict:

```markdown
## Conflict: <short name for the fact, e.g. "Director review deadline">

- **File A** (`docs/proposal-form.md`, near "the deadline for..."): says **March 15**
- **File B** (`docs/meeting-notes-week3.md`, near "revised timeline"): says **March 22**

**Why it matters:** <one line — why this fact needs to be right before the client call>
**Suggested next step:** Ask <whoever seems closest to the source, if inferable> to confirm.
```

List every conflict found this way before moving on — do not stop after the first one.

## Step 5 — Always produce the densified summary

Whether or not conflicts were found, always produce one consolidated, client-ready fact sheet. This is the actual deliverable — the clarification file is a side effect for the team, not the main output.

- One line or short bullet per fact. This is about density, not narrative — a fact sheet, not an essay.
- **Consistent facts:** state the value plainly. Optionally cite which files confirm it if that adds confidence (e.g. "confirmed in 3 notes").
- **Conflicting facts:** do NOT pick one. Mark clearly, e.g. `UNRESOLVED (see clarification/conflicts-...md) — candidates: March 15 / March 22`, so the reader has context without needing to open the other file.
- Group facts under sensible headers (by topic, by project area) rather than dumping a flat list.

## Output locations

Write both outputs as real files so Thomas can open and share them:
- `clarification/<file>.md` — only created if at least one conflict was found
- `densified-summary.md` (or a name reflecting the project, e.g. `<project>-summary.md`) — always created

If working in this computer environment, save both under `/mnt/user-data/outputs/` (creating the `clarification` subfolder there too), then share them with `present_files`. Mention briefly in chat whether any conflicts were found, but do not restate the whole clarification file inline.
