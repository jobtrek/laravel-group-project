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

**Stack:** Laravel 10/11/12 + Blade templates + Laravel Sail (Docker + PostgreSQL)  
**Scope:** Security vulnerabilities, code quality, and best practices — no Inertia, no SPA, pure server-rendered Blade.

---

## How to Run the Audit

### 1. Map the Codebase First

Before issuing any findings, scan the structure. Ask the user to share or look at:

```
app/
├── Http/
│   ├── Controllers/      ← logic leaking here?
│   ├── Middleware/        ← custom auth guards?
│   └── Requests/          ← FormRequests present?
├── Models/                ← fillable/guarded set correctly?
├── Policies/              ← authorization centralized?
├── Services/ or Actions/  ← or is business logic in controllers?
resources/views/            ← raw {{ }} vs {!! !!} usage?
routes/
├── web.php                 ← auth middleware on all protected routes?
└── console.php             ← scheduler entries
database/migrations/        ← nullable columns, index coverage
config/
├── app.php                 ← APP_DEBUG, APP_ENV
└── session.php             ← session driver, secure cookies
.env / .env.example         ← secrets committed?
```

If the user pastes files, read them. If they give a repo, `view` the directories above before issuing findings.

---

## Audit Checklist — Ordered by Severity

Work through these in order. Report issues with: **severity**, **location**, **what's wrong**, **fix**.

---

### 🔴 Critical — Fix Before Any Deployment

#### 1. XSS via `{!! !!}` in Blade
```blade
{{-- ❌ Unescaped output — XSS if $user->bio is user-supplied --}}
{!! $user->bio !!}

{{-- ✅ Always escape user-controlled data --}}
{{ $user->bio }}

{{-- ✅ Only use {!! !!} for trusted, sanitized HTML (e.g. markdown rendered server-side) --}}
{!! $post->rendered_html !!}  {{-- only OK if sanitized at write-time --}}
```

**How to spot:** `grep -rn "{!!" resources/views/` — every hit needs justification.

---

#### 2. Mass Assignment Vulnerability
```php
// ❌ Empty $guarded + create() from request = fills ANY column
protected $guarded = [];
User::create($request->all()); // attacker sets is_admin=1

// ✅ Explicit $fillable
protected $fillable = ['name', 'email', 'password'];

// ✅ Or use $request->validated() from a FormRequest (best)
User::create($request->validated());
```

**Check:** every model — is `$guarded = []` paired with `->create($request->all())`?

---

#### 3. Missing CSRF Protection
Laravel's `VerifyCsrfToken` middleware is on `web` routes by default — but check:

```php
// ❌ Routes removed from CSRF protection without reason
protected $except = [
    'webhook/*',
    '*',  // ← nuclear option, breaks all protection
];
```

```blade
{{-- ✅ Every POST/PUT/DELETE form must have --}}
<form method="POST" action="/orders">
    @csrf
    ...
</form>
```

**Check:** `app/Http/Middleware/VerifyCsrfToken.php` — what's in `$except`? Any form without `@csrf`?

---

#### 4. SQL Injection via Raw Queries
```php
// ❌ Never interpolate user input into raw SQL
DB::select("SELECT * FROM users WHERE email = '{$request->email}'");
DB::statement("DELETE FROM logs WHERE user_id = {$id}");

// ✅ Always use bindings
DB::select('SELECT * FROM users WHERE email = ?', [$request->email]);
DB::table('users')->where('email', $request->email)->first(); // ← preferred
```

**Check:** `grep -rn "DB::select\|DB::statement\|DB::raw" app/` — each hit needs review.

---

#### 5. Unauthenticated Routes to Protected Resources
```php
// ❌ Forgot auth middleware on sensitive route
Route::get('/admin/users', [AdminController::class, 'index']); // no middleware!

// ✅ Group everything that requires auth
Route::middleware(['auth'])->group(function () {
    Route::resource('admin/users', AdminController::class);
});
```

**Check:** every route in `web.php` — does it actually need `auth`? Is `auth` the right guard?

---

### 🟠 High — Fix Before Going Live

#### 6. Authorization Missing (No Policies / Manual Checks)
```php
// ❌ Relying only on "does the user exist?" instead of "can this user do this?"
public function destroy(Post $post) {
    $post->delete(); // any logged-in user can delete anyone's post
}

// ✅ Use Policies
public function destroy(Post $post) {
    $this->authorize('delete', $post);
    $post->delete();
}
```

```blade
{{-- ✅ Hide UI for unauthorized actions --}}
@can('delete', $post)
    <form method="POST" action="{{ route('posts.destroy', $post) }}">
        @csrf @method('DELETE')
        <button>Delete</button>
    </form>
@endcan
```

📖 `laravel.com/docs/12.x/authorization`

---

#### 7. Sensitive Data in `.env` Committed to Git
```bash
# Check for .env in git history
git log --all --full-history -- ".env"
git grep -l "APP_KEY\|DB_PASSWORD\|MAIL_PASSWORD" $(git log --pretty=format:%H)
```

`.env` must be in `.gitignore`. `.env.example` should have all keys with **empty or placeholder values** — never real credentials.

---

#### 8. APP_DEBUG=true in Production
```env
# ❌ Exposes stack traces, DB credentials, env values to users
APP_DEBUG=true
APP_ENV=production

# ✅
APP_DEBUG=false
APP_ENV=production
```

Also check `config/app.php` — `'debug' => env('APP_DEBUG', false)` is correct (false as default).

---

#### 9. Insecure File Uploads
```php
// ❌ Trust the client-supplied extension
$ext = $request->file('avatar')->getClientOriginalExtension();
$request->file('avatar')->storeAs('avatars', 'photo.' . $ext);

// ✅ Validate MIME type server-side and use guessExtension()
$request->validate([
    'avatar' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
]);
$ext = $request->file('avatar')->guessExtension(); // from actual MIME
$path = $request->file('avatar')->store('avatars');
```

**Critical:** uploads must NEVER be stored in `public/` directly accessible — use `storage/app/private/` and serve via a controller.  
📖 `laravel.com/docs/12.x/filesystem#file-uploads`

---

#### 10. Unvalidated Redirects
```php
// ❌ Redirect to user-supplied URL = open redirect
return redirect($request->input('next'));

// ✅ Validate it's an internal path
$next = $request->input('next', '/dashboard');
if (!str_starts_with($next, '/') || str_starts_with($next, '//')) {
    $next = '/dashboard';
}
return redirect($next);
```

---

### 🟡 Medium — Code Quality & Best Practices

#### 11. N+1 Queries in Blade Views
```php
// ❌ Each post loops → N queries for author
$posts = Post::all(); // then in Blade: $post->author->name

// ✅ Eager load what you'll use in the view
$posts = Post::with(['author', 'tags'])->paginate(20);
```

**How to spot:** Install `barryvdh/laravel-debugbar` in dev and look for repeated identical queries.  
📖 `laravel.com/docs/12.x/eloquent-relationships#eager-loading`

---

#### 12. Inline Validation in Controllers
```php
// ❌ Validation belongs in a FormRequest, not here
public function store(Request $request) {
    $request->validate([...]);
}

// ✅
php artisan make:request StorePostRequest
```

📖 `laravel.com/docs/12.x/validation#form-request-validation`

---

#### 13. Business Logic in Blade Views
```blade
{{-- ❌ Logic in views is untestable and fragile --}}
@if ($user->orders()->where('status', 'pending')->count() > 0)
    ...
@endif

{{-- ✅ Compute in controller, pass a flag --}}
{{-- Controller: $hasPendingOrders = $user->hasPendingOrders(); --}}
@if ($hasPendingOrders)
```

---

#### 14. Hardcoded URLs in Blade
```blade
{{-- ❌ Breaks on any route change --}}
<a href="/users/{{ $user->id }}/edit">Edit</a>
<form action="/posts/{{ $post->id }}" method="POST">

{{-- ✅ Always use route() helper --}}
<a href="{{ route('users.edit', $user) }}">Edit</a>
<form action="{{ route('posts.update', $post) }}" method="POST">
```

---

#### 15. Missing `@method` for PUT/PATCH/DELETE
```blade
{{-- ❌ HTML forms only support GET/POST — Laravel needs the spoof --}}
<form method="PUT" action="{{ route('posts.update', $post) }}">

{{-- ✅ --}}
<form method="POST" action="{{ route('posts.update', $post) }}">
    @csrf
    @method('PUT')
    ...
</form>
```

---

#### 16. Paginating Large Collections
```php
// ❌ Loads every row into memory
$users = User::all();

// ✅ Always paginate in list views
$users = User::latest()->paginate(25);

// In Blade:
{{ $users->links() }} {{-- renders pagination links --}}
```

📖 `laravel.com/docs/12.x/pagination`

---

#### 17. Session & Cookie Security (Sail / Production)
```php
// config/session.php — check these for production
'driver'          => env('SESSION_DRIVER', 'database'), // not 'file' in multi-server
'secure'          => env('SESSION_SECURE_COOKIE', true), // HTTPS only
'http_only'       => true,                               // no JS access
'same_site'       => 'lax',                              // CSRF protection layer
```

For Sail local dev, `secure` can stay `false`. For production → always `true`.

---

#### 18. Logging Sensitive Data
```php
// ❌ Don't log full request payloads — passwords, tokens end up in logs
Log::info('Login attempt', $request->all());

// ✅ Log only what you need to debug
Log::info('Login attempt', ['email' => $request->email, 'ip' => $request->ip()]);
```

---

### 🔵 Sail-Specific Checks

#### 19. Exposed Ports in `docker-compose.yml`
```yaml
# ❌ PostgreSQL exposed to 0.0.0.0 — accessible from outside the container
ports:
    - '5432:5432'  # dangerous in any environment with a public IP

# ✅ Bind to localhost
ports:
    - '127.0.0.1:5432:5432'
```

Also check: is `FORWARD_DB_PORT` in `.env` necessary for your workflow? Remove if not.

---

#### 20. `.env` Database Credentials in Sail
In the default Sail `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=pgsql        # ← the Docker service name, correct for Sail
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password # ❌ change this, even locally
```

For any non-local environment: strong, unique `DB_PASSWORD`, `APP_KEY` rotated, `APP_ENV=production`.

---

#### 21. Sail in Production
**Sail is not intended for production.** If the project will be deployed:
- Strip `docker-compose.yml` / Sail entirely from the production image
- Use a proper Docker setup (or Dokploy, Forge, Railway) with hardened Dockerfile
- Remove `vendor/laravel/sail` in `composer.json` `require-dev` (it likely already is)

---

## Audit Output Format

For each issue found, report as:

```
[SEVERITY] Title
Location: app/Http/Controllers/PostController.php:42
Problem:  ...
Fix:      ...
Docs:     laravel.com/docs/12.x/...
```

Then close with a summary table:

| Severity | Count | Biggest Risk |
|----------|-------|--------------|
| 🔴 Critical | N | ... |
| 🟠 High     | N | ... |
| 🟡 Medium   | N | ... |
| 🔵 Sail     | N | ... |

---

## Quick Grep Commands

Run these on the project root to find common issues fast:

```bash
# XSS candidates
grep -rn "{!!" resources/views/

# Raw SQL (injection risk)
grep -rn "DB::select\|DB::statement\|DB::raw" app/

# Mass assignment with $request->all()
grep -rn "->create(\$request->all\|->fill(\$request->all" app/

# Missing @csrf (forms without it)
grep -rn "<form" resources/views/ | grep -v "@csrf"

# Hardcoded URLs
grep -rn "href=\"/" resources/views/
grep -rn "action=\"/" resources/views/

# APP_DEBUG check
grep -n "APP_DEBUG" .env

# .env in git
git status --short | grep ".env$"

# Exposed DB port
grep -n "5432:5432" docker-compose.yml
```

---

## Suggestion Rules

- **Always check Critical issues first** — if any exist, flag them before anything else
- **Don't audit what isn't there** — if there are no file uploads, skip #9
- **Cite exact line numbers** when the user has shared code
- **Don't suggest packages** unless native Laravel genuinely can't solve it
- **Sail-specific findings** only if there's a `docker-compose.yml` / Sail in the project
- **Pair every finding with a fix** — not just "this is bad"