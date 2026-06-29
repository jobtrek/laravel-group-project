# Code Quality — Medium (🟡)

---

## 11. N+1 Queries in Blade

```php
// ❌ Each post loops → N queries for author
$posts = Post::all(); // then in Blade: $post->author->name

// ✅ Eager load what you'll use in the view
$posts = Post::with(['author', 'tags'])->paginate(20);
```

Install `barryvdh/laravel-debugbar` in dev to spot repeated identical queries.

---

## 12. Inline Validation in Controllers

```php
// ❌ Validation belongs in a FormRequest, not here
public function store(Request $request) {
    $request->validate([...]);
}

// ✅
php artisan make:request StorePostRequest
// Then type-hint it: public function store(StorePostRequest $request)
```

---

## 13. Business Logic in Blade Views

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

## 14. Hardcoded URLs in Blade

```blade
{{-- ❌ Breaks on any route change --}}
<a href="/users/{{ $user->id }}/edit">Edit</a>
<form action="/posts/{{ $post->id }}" method="POST">

{{-- ✅ Always use the route() helper --}}
<a href="{{ route('users.edit', $user) }}">Edit</a>
<form action="{{ route('posts.update', $post) }}" method="POST">
```

---

## 15. Missing `@method` for PUT/PATCH/DELETE

```blade
{{-- ❌ HTML forms only support GET/POST --}}
<form method="PUT" action="{{ route('posts.update', $post) }}">

{{-- ✅ --}}
<form method="POST" action="{{ route('posts.update', $post) }}">
    @csrf
    @method('PUT')
    ...
</form>
```

---

## 16. Unpaginated Large Collections

```php
// ❌ Loads every row into memory
$users = User::all();

// ✅ Always paginate in list views
$users = User::latest()->paginate(25);
```

```blade
{{ $users->links() }} {{-- renders pagination UI --}}
```

---

## 17. Session & Cookie Security

```php
// config/session.php — verify for production
'driver'    => env('SESSION_DRIVER', 'database'), // not 'file' in multi-server setups
'secure'    => env('SESSION_SECURE_COOKIE', true), // HTTPS only
'http_only' => true,                               // no JS access
'same_site' => 'lax',                              // CSRF protection layer
```

Sail local dev: `secure` can be `false`. Production: always `true`.

---

## 18. Logging Sensitive Data

```php
// ❌ Passwords, tokens end up in log files
Log::info('Login attempt', $request->all());

// ✅ Log only what you need to debug
Log::info('Login attempt', ['email' => $request->email, 'ip' => $request->ip()]);
```
