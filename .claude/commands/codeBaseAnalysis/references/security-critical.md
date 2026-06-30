# Security — Critical (🔴)

Fix these before any deployment.

---

## 1. XSS via `{!! !!}` in Blade

```blade
{{-- ❌ Unescaped output — XSS if $user->bio is user-supplied --}}
{!! $user->bio !!}

{{-- ✅ Always escape user-controlled data --}}
{{ $user->bio }}

{{-- ✅ {!! !!} only for trusted, sanitized HTML (e.g. markdown rendered server-side) --}}
{!! $post->rendered_html !!}
```

**Grep:** `grep -rn "{!!" resources/views/` — every hit needs justification.

---

## 2. Mass Assignment

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

## 3. Missing CSRF Protection

Laravel's `VerifyCsrfToken` middleware is on `web` routes by default — but check:

```php
// ❌ Removing protection without reason
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

## 4. SQL Injection via Raw Queries

```php
// ❌ Never interpolate user input into raw SQL
DB::select("SELECT * FROM users WHERE email = '{$request->email}'");
DB::statement("DELETE FROM logs WHERE user_id = {$id}");

// ✅ Always use bindings
DB::select('SELECT * FROM users WHERE email = ?', [$request->email]);
DB::table('users')->where('email', $request->email)->first(); // preferred
```

**Grep:** `grep -rn "DB::select\|DB::statement\|DB::raw" app/` — each hit needs review.

---

## 5. Unauthenticated Routes to Protected Resources

```php
// ❌ Forgot auth middleware on sensitive route
Route::get('/admin/users', [AdminController::class, 'index']); // no middleware!

// ✅ Group everything requiring auth
Route::middleware(['auth'])->group(function () {
    Route::resource('admin/users', AdminController::class);
});
```

**Check:** every route in `web.php` — does it need `auth`? Is `auth` the right guard?
