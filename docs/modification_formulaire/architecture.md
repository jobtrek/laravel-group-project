SUBMITTER                          DIRECTOR
    │
    ▼
┌─────────────────┐
│  3-Step Form     │   fills in title, description,
│  (steps 1-2-3)   │   adds N phases, submits
└────────┬─────────┘
         │ status = Submitted
         ▼
┌─────────────────────────────┐
│  Direction Review Page       │ ◀── director opens this
│  (all fields shown at once,  │
│   read-only, with checkbox   │
│   + comment box per field)   │
└────────┬─────────────────────┘
         │ director checks some boxes,
         │ writes a comment in each, submits review
         │
   ┌─────┴─────┐
   │           │
no boxes    1+ boxes
checked     checked
   │           │
   ▼           ▼
Approved    NeedsRevision ──▶ queued email sent to submitter
                                       │
                                       ▼
                          SUBMITTER clicks link in email
                                       │
                                       ▼
                          ┌─────────────────────────┐
                          │   Revision Page          │
                          │   (ONLY the flagged      │
                          │   fields, prefilled,     │
                          │   comment shown above)   │
                          └────────┬─────────────────┘
                                   │ edits, resubmits
                                   ▼
                          status = UnderReview again
                                   │
                                   └──▶ back to Direction Review Page



That's the full loop closed: flag → store exception row → email (project ID only) → controller queries exceptions → render just those → write back to the real column/row → mark resolved → state flips back to UnderReview, and the director sees it again on the same review page from before.