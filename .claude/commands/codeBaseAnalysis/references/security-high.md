# Security — High (🟠)

Fix these before going live.

---

## 6. Missing Authorization (No Policies)

```php
// ❌ Any logged-in user can delete anyone's post
public function destroy(Post $post) {
    $post->delete();
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

---

## 7. Sensitive Data in `.env` Committed to Git

```bash
git log --all --full-history -- ".env"
git grep -l "APP_KEY\|DB_PASSWORD\|MAIL_PASSWORD" $(git log --pretty=format:%H)
```

`.env` must be in `.gitignore`. `.env.example` should have all keys with **empty or placeholder values** — never real credentials.

---

## 8. APP_DEBUG=true in Production

```env
# ❌ Exposes stack traces, DB credentials, env values to users
APP_DEBUG=true
APP_ENV=production

# ✅
APP_DEBUG=false
APP_ENV=production
```

Check `config/app.php` — `'debug' => env('APP_DEBUG', false)` is correct (false as the default).

---

## 9. Insecure File Uploads

```php
// ❌ Trust the client-supplied extension
$ext = $request->file('avatar')->getClientOriginalExtension();
$request->file('avatar')->storeAs('avatars', 'photo.' . $ext);

// ✅ Validate MIME type server-side and use guessExtension()
$request->validate([
    'avatar' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
]);
$ext = $request->file('avatar')->guessExtension(); // from actual MIME
$path = $request->file('avatar')->store('avatars'); // → storage/app/private/
```

Uploads must **never** be stored in `public/` — use `storage/app/private/` and serve via a controller.

---

## 10. Unvalidated Redirects

```php
// ❌ Open redirect via user-supplied URL
return redirect($request->input('next'));

// ✅ Validate it's an internal path
$next = $request->input('next', '/projects');
if (!str_starts_with($next, '/') || str_starts_with($next, '//')) {
    $next = '/projects';
}
return redirect($next);
```
