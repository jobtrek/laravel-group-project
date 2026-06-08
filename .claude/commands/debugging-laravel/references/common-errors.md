# Common Laravel Errors

## Contents
- Undefined variable in view
- Attempt to read property on null
- Class not found
- Target class is not instantiable
- SQLSTATE integrity constraint violation
- SQLSTATE column not found
- Method not supported (405)
- CSRF token mismatch (419)
- ModelNotFoundException (404)
- Config/cache not reflecting changes

---

## Undefined variable `$x` in view

**Cause:** Controller didn't pass the variable to the view.

```php
// ❌ Missing
return view('posts.show');

// ✅
return view('posts.show', ['post' => $post]);
// or
return view('posts.show', compact('post'));
```

---

## Attempt to read property on null

**Cause:** A relationship returned null and you're accessing a property on it.

```php
// ❌ $post->user is null
{{ $post->user->name }}

// ✅ Null-safe operator
{{ $post->user?->name }}

// ✅ Check first in Blade
@if($post->user) {{ $post->user->name }} @endif
```

---

## Class `App\Http\Controllers\X` not found

**Cause 1:** Typo in the route or use statement.
**Cause 2:** File exists but Composer's autoloader doesn't know about it yet.

```bash
composer dump-autoload
```

---

## Target class `X` is not instantiable

**Cause:** A constructor dependency can't be resolved — usually you're type-hinting an interface that hasn't been bound in a service provider.

For beginners: ensure all constructor parameters are concrete classes. Interfaces require a service provider binding to work.

---

## SQLSTATE[23000]: Integrity constraint violation

Two common causes:

**FK violation** — the foreign key value doesn't exist in the referenced table.
```
// user_id 999 doesn't exist in users
// → Create the parent record first, or check the ID you're passing
```

**Unique violation** — the value already exists in a unique column.
```php
// → Validate uniqueness before inserting
'email' => ['required', 'email', Rule::unique('users', 'email')],
```

---

## SQLSTATE[42S22]: Column not found

**Cause:** You're querying a column that doesn't exist — either a typo or a pending migration.

```bash
php artisan migrate          # run pending migrations
php artisan migrate:status   # see which have run
```

---

## The GET method is not supported (405)

**Cause:** Your form submits to a route that only accepts POST, or you're visiting a POST-only route in the browser.

```blade
<form method="POST" action="{{ route('posts.store') }}">
    @csrf
    ...
</form>
```

For PUT/PATCH/DELETE routes, add `@method('PUT')` inside the form.

---

## CSRF token mismatch (419)

**Cause:** Form is missing `@csrf`, or the session expired.

```blade
<form method="POST" action="...">
    @csrf
    ...
</form>
```

---

## No query results for model (404)

`findOrFail()` and `firstOrFail()` throw a `ModelNotFoundException` (which Laravel turns into a 404) when no record matches.

Check the ID being passed, or confirm the record exists:
```bash
php artisan tinker
>>> App\Models\Post::find(1);  // returns null if it doesn't exist
```

---

## Config or env changes not applying

Laravel caches config and views. After changing `.env` or `config/`:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```
