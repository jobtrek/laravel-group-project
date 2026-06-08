---
name: debugging-laravel
description: Guides through diagnosing Laravel application errors — reading log files, interpreting stack traces, choosing between dd/dump/logger, and identifying common error types by HTTP status code. Use when the user hits an error, exception, blank page, 500 error, or asks why something isn't working in their Laravel app.
---

# Debugging Laravel

## Step 1: Read the log first

```bash
tail -n 50 storage/logs/laravel.log
```

The log has the full exception message and stack trace. **Always check here before anything else.**

If the page shows a generic error (or a blank page), `APP_DEBUG` may be `false` — set it to `true` locally in `.env` to see the actual error.

## Step 2: Match the HTTP status to the cause

| Status | Likely cause |
|--------|-------------|
| 404 | Route doesn't exist — run `php artisan route:list` |
| 403 | Authorization failed — check Policy or `authorize()` call |
| 419 | CSRF token missing — form needs `@csrf` |
| 422 | Validation failed |
| 500 | Unhandled exception — check `storage/logs/laravel.log` |
| Blank page | `APP_DEBUG=false` hiding errors |

For specific error messages and fixes, see [references/common-errors.md](references/common-errors.md).

## Step 3: Isolate the problem

| Tool | When |
|------|------|
| `dd($var)` | Stop execution and inspect — use to confirm a value mid-flow |
| `dump($var)` | Print and continue — use inside loops |
| `logger($var)` | Write to log without stopping the user |
| `Log::info('label', ['key' => $val])` | Structured log entry |

```php
dd($request->all());          // stop and inspect the request
dump($item->id);               // inside a loop — see all values
logger('reached checkout', ['user_id' => $user->id]);
```

**Remove all `dd()` and `dump()` before committing.**

## Step 4: Read the stack trace

Stack traces read bottom-to-top (bottom = where it started, top = where it failed).

```
ErrorException: Undefined variable $user          ← the error
  at app/Http/Controllers/PostController.php:24   ← your code — fix here
  at vendor/laravel/framework/...                 ← Laravel internals — usually ignore
```

Focus on lines in `app/` — those are your files. Lines in `vendor/` are usually a symptom, not the cause.

## Useful diagnostic commands

```bash
php artisan route:list               # all registered routes
php artisan route:list --name=posts  # filter by route name
php artisan config:clear             # clear config cache (fixes "env change not applying")
php artisan cache:clear              # clear app cache
php artisan view:clear               # clear compiled Blade views
php artisan migrate:status           # check which migrations have run
```

## Test queries live with Tinker

```bash
php artisan tinker
```
```php
User::find(1);
User::where('email', 'test@test.com')->first();
Post::with('user')->first();     // test eager loading
```

📖 `laravel.com/docs/12.x/errors`
