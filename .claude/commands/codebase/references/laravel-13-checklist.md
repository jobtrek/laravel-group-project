# Laravel 13 Reference Checklist

Use this to decide which official doc page(s) to fetch (via web_fetch, starting
from a web_search if the exact URL isn't already in context) before judging a
specific category of backend finding. Laravel ships an annual major version,
and conventions/attribute-based APIs introduced in 13.x change what "best
practice" means compared to 11.x/12.x — don't grade Laravel 13 code against an
older mental model.

You may use agent-browser to look up this information.

| Category in the code | Fetch this doc | What changed / what to check in 13.x |
|---|---|---|
| Mass assignment (`$fillable`, `$guarded`, `create()`, `update()`) | `https://laravel.com/docs/13.x/eloquent` | Laravel 13 adds `#[Fillable([...])]` and `#[Unguarded]` PHP attributes as the modern alternative to the `$fillable`/`$guarded` properties. Either style is valid, but a model with **neither** an attribute nor a property defined, that still calls `create()`/`update()` with raw request arrays, is unprotected. `#[Unguarded]` (or `$guarded = []`) removes protection entirely — flag it if the model is ever filled from raw request input rather than hand-picked/validated data. |
| Authorization (controllers, admin actions, resource ownership checks) | `https://laravel.com/docs/13.x/authorization` | Laravel 13 has a leaner base controller — it no longer reliably provides a `$this->authorize()` helper by inheritance. The current idiom is calling `Gate::authorize()` directly, or using the `#[Authorize]` attribute on controller methods, or `#[UsePolicy]` on the model. A controller action that mutates/deletes a model with **no** Gate/Policy check anywhere in its path is a broken-authorization finding regardless of which of these mechanisms is missing. |
| Middleware / route protection | `https://laravel.com/docs/13.x/middleware` | Laravel 13 supports the `#[Middleware]` attribute directly on controller classes/methods as an alternative to route-file middleware chains — check both places before concluding a route is unprotected. |
| Validation | `https://laravel.com/docs/13.x/validation` | Controller methods that pull request input directly (`$request->all()`, `$request->input()`) into a create/update call instead of using a Form Request or `$request->validate()` are a validation gap — flag missing/absent validation before mass-assignment, not just the mass-assignment property itself. |
| Raw queries / injection | `https://laravel.com/docs/13.x/queries` and `https://laravel.com/docs/13.x/database` | `DB::raw()`, `whereRaw()`, `selectRaw()`, or string-concatenated query fragments built from request input are injection risks even when wrapped in the query builder — parameter binding must be used for any interpolated value. |
| Eloquent relationships / N+1 | `https://laravel.com/docs/13.x/eloquent-relationships` | Loops that lazy-load a relationship per iteration instead of eager-loading (`with()`) are a performance/best-practice finding, not just a style note — at scale this is a real availability issue. |
| Queues / jobs | `https://laravel.com/docs/13.x/queues` | Laravel 13 adds class-based queue routing (`Queue::route(...)`) and job attributes (`#[Tries]`, `#[Backoff]`, `#[Timeout]`, `#[FailOnTimeout]`). A job with no failure/backoff handling for an operation that can plausibly fail (external API calls, payment capture, email delivery) is an edge-case gap worth flagging. |
| Controllers / fat controllers | `https://laravel.com/docs/13.x/controllers` | Laravel 13's controllers are intentionally leaner; business logic that belongs in an Action/Service class but lives inline in a controller method is a best-practice deviation, especially when it's duplicated across multiple controller methods. |

## Notes on this list

- This table is a starting point, not exhaustive. If the code surfaces a
  category not listed here (file storage, notifications, events, casts,
  etc.), search for `"Laravel 13.x" <topic>` and fetch the relevant official
  doc page before writing the finding.
- Prefer `laravel.com/docs/13.x/...` over blog posts/tutorials when they
  disagree — blogs may describe older versions' conventions.
- PHP minimum for Laravel 13 is 8.3; code relying on syntax/behavior only
  valid pre-8.3 alongside a Laravel 13 `composer.json` is itself worth
  flagging as an inconsistency.
