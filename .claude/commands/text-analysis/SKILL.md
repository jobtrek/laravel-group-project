---
name: client-notes-densifier
description: Use this skill whenever Thomas has a folder of overlapping markdown notes (e.g. a /docs directory on a client project) that need to be condensed into one trustworthy summary before a client-side or team discussion. Trigger on phrases like "densify these notes", "check my docs for conflicting info", "clean up these notes before the client call", "some of these notes are wrong", "find where the team disagrees", or any request to consolidate scattered project documentation. The skill reads only markdown/text files — it deliberately skips MLD diagrams, PNGs, and other binary/image files since those aren't meant to be parsed as text. Whenever the same fact is stated with different values across files, the skill ALWAYS stops and writes a clarification/*.md file pinpointing exactly where the conflict lives, before producing anything else — it never silently guesses which value is correct. It then always produces a clean, densified summary doc, flagging any unresolved conflicts inline instead of papering over them. Trigger this even if Thomas doesn't use the word "densify" — "are these docs consistent" or "summarize /docs for the client" are the same task.
---

# Client Notes Densifier

Cross-reference overlapping markdown notes, surface conflicts loudly, and produce one clean client-ready fact sheet.

**Core rule: never silently pick a winner between conflicting values.** Flag it, point at the exact files, and let the human resolve it.

---

## Quick reference

| Step | Action |
|------|--------|
| 1 | Scope the scan — find all `.md` files, skip images and diagram exports |
| 2 | Extract atomic facts per file (values, names, dates, rules, counts) |
| 3 | Group facts across files; classify as consistent or conflicting |
| 4 | On any conflict — write `clarification/<file>.md` first, before the summary |
| 5 | Always produce the densified summary, marking unresolved conflicts inline |

For full step-by-step detail, load:
`.claude/commands/text-analysis/references/workflow.md`

---

## Outputs

- `clarification/<file>.md` — created only if at least one conflict is found
- `densified-summary.md` (or `<project>-summary.md`) — always created

## Gotchas

- Skip `.png`, `.jpg`, `.jpeg`, `.svg`, `.webp`, `.drawio`, `.mld` entirely — do not attempt to extract facts from images
- A sentence like "I think we should maybe..." is an opinion, not a fact to cross-reference
- Multiple files independently agreeing on a value is worth noting in the summary as confirmed
- Do not resolve conflicts by picking the "more recent" or "more official" source — only the team can do that
