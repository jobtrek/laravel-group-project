---
name: laravel-blade-audit
description: >
    Security and code quality audit for traditional Laravel projects using Blade templates (no Inertia,
    no API layer). Triggers when Thomas asks to "audit", "review", "analyse", or "check" a Laravel+Blade
    codebase, or when he says things like "is this secure?", "any issues with my code?", "code review
    my Laravel project", "check for vulnerabilities", "best practices check", or pastes code and asks
    what's wrong. Also trigger when he mentions Sail, PSql, Blade files, or asks if his Laravel project
    is production-ready. Be pushy — if a Laravel + Blade project is being discussed and there's no
    recent audit, suggest running one.
---

# Laravel Blade Codebase Audit

**Stack:** Laravel 13 + Blade + Alpine.js + Laravel Sail (Docker + PostgreSQL)
**Scope:** Security vulnerabilities, code quality, best practices — no Inertia, no SPA, pure server-rendered Blade.

---

## Step 1: Map the Codebase

Before issuing findings, scan this structure:

```
app/
├── Http/
│   ├── Controllers/      ← logic leaking here?
│   ├── Middleware/        ← custom auth guards?
│   └── Requests/          ← FormRequests present?
├── Models/                ← fillable/guarded set correctly?
├── Policies/              ← authorization centralized?
└── Services/ or Actions/  ← or is business logic in controllers?
resources/views/            ← raw {{ }} vs {!! !!} usage?
routes/
├── web.php                 ← auth middleware on all protected routes?
└── console.php             ← scheduler entries
database/migrations/        ← nullable columns, index coverage
config/
├── app.php                 ← APP_DEBUG, APP_ENV
└── session.php             ← session driver, secure cookies
.env / .env.example         ← secrets committed?
docker-compose.yml          ← exposed ports?
```

---

## Step 2: Run Quick Grep Scans

The system has rg installed so use rg, to list all the commands launch rg --help

```bash
rg -rn '\{!!' resources/views/                                      # XSS candidates
rg -rn "DB::(select|statement|raw)" app/              # raw SQL
rg -rn '->(create|fill)\(\$request->all' app/   # mass assignment
rg -rn "<form" resources/views/ | grep -v "@csrf"               # missing CSRF
rg -rn "href=\"/" resources/views/                              # hardcoded URLs
rg -rn "action=\"/" resources/views/                            # hardcoded actions
rg -n "APP_DEBUG" .env                                          # debug flag
git ls-files .env                             # .env in git
rg -n "5432:5432" docker-compose.yml 2>/dev/null                # exposed DB port
```

---

## Step 3: Audit Checklist

Work through each tier in order. Load the reference file for each tier before auditing.

### 🔴 Critical — load `.claude/commands/codeBaseAnalysis/references/security-critical.md` before auditing any controller, route, or Blade view

1. XSS via `{!! !!}` in Blade
2. Mass assignment (`$guarded = []` + `->create($request->all())`)
3. Missing CSRF (`@csrf` / `VerifyCsrfToken::$except`)
4. SQL injection via raw queries
5. Unauthenticated routes to protected resources

### 🟠 High — load `.claude/commands/codeBaseAnalysis/references/security-high.md` once critical checks are done

6. Missing authorization (no Policies, no `$this->authorize()`)
7. `.env` secrets committed to git
8. `APP_DEBUG=true` in production
9. Insecure file uploads (client extension trusted, stored in `public/`)
10. Unvalidated redirects (open redirect via user-supplied URL)

### 🟡 Medium — load `.claude/commands/codeBaseAnalysis/references/code-quality.md` when auditing Blade views or controller structure

11. N+1 queries in Blade (missing eager loading)
12. Inline validation in controllers (missing FormRequests)
13. Business logic in Blade views
14. Hardcoded URLs instead of `route()` helper
15. Missing `@method('PUT'/'DELETE')` on forms
16. Unpaginated large collections (`Model::all()`)
17. Session & cookie misconfiguration
18. Logging sensitive data (passwords, tokens)

### 🔵 Sail/Docker — load `.claude/commands/codeBaseAnalysis/references/sail-docker.md` only if `docker-compose.yml` is present

19. PostgreSQL port exposed to `0.0.0.0`
20. Weak `.env` credentials in Sail config
21. Sail used in production

---

## Audit Output Format

For each issue found:

```
[SEVERITY] Title
Location: app/Http/Controllers/PostController.php:42
Problem:  ...
Fix:      ...
```

Close with a summary table:

| Severity    | Count | Biggest Risk |
| ----------- | ----- | ------------ |
| 🔴 Critical | N     | ...          |
| 🟠 High     | N     | ...          |
| 🟡 Medium   | N     | ...          |
| 🔵 Sail     | N     | ...          |

---

## Rules

- Check Critical first — flag them before anything else
- Don't audit what isn't there (no uploads → skip #9; no `docker-compose.yml` → skip Sail section)
- Cite exact line numbers when the user has shared code
- Don't suggest packages unless native Laravel can't solve it
- Pair every finding with a fix
