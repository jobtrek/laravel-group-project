# Archive

Historical material superseded by `docs/Source_of_truth.md` and `docs/database/schema.md`. Kept for traceability, not for reference — if something here conflicts with either of those two files, they win.

- `raw_docs/` — original client interview notes, brainstorm files, and early drafts (`thomas.md`, `tiziano.md`, `correlation.md`, `states.md`, `user-stories.md`, `guide_mode_state.md`, `notion-notes.md`, etc.) that `Source_of_truth.md` was synthesized from. Several describe an older state machine (`DraftState`, `SuspendedState`, `ReadyState`, ...) that no longer matches the codebase.
- `clarification/` — conflict logs from the docs-densification process. `conflicts-2026-06-24.md` is fully resolved. `conflicts-2026-06-25.md` has conflict B resolved as of 2026-07-08; conflicts A, C, D are still open questions.
- `mld.mermaid`, `MLD.png`, `MLD Diagram.drawio.png` — an earlier, hand-authored logical data model. Superseded by `docs/database/MLD.mermaid`, which is generated from and matches the actual schema.
