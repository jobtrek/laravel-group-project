---
name: modeling-relationships
description: Explains Eloquent relationship types — one-to-one, one-to-many, and many-to-many — including the correct migration columns, model method definitions, and inverse relationships. Use when the user asks how to connect two models, which relationship type to use, how to set up a foreign key, or how to define a relationship method in Laravel.
---

# Modeling Eloquent Relationships

## Which type do I need?

| Scenario | Type |
|----------|------|
| User has one profile | `hasOne` / `belongsTo` |
| User has many posts | `hasMany` / `belongsTo` |
| Post has many tags, tag has many posts | `belongsToMany` (both sides) |

For full method signatures, migration columns, and pivot table operations see [references/relationship-types.md](references/relationship-types.md).

## Every relationship needs three things

1. A **foreign key column** in the migration (on the child table)
2. A **method on the parent model** (`hasMany`, `hasOne`, etc.)
3. A **method on the child model** (`belongsTo`)

## One-to-many (most common)

**Migration** — FK goes on the child table:
```php
// create_posts_table migration
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
```

**Parent (User):**
```php
public function posts(): HasMany
{
    return $this->hasMany(Post::class);
}
```

**Child (Post):**
```php
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

**Usage:**
```php
$user->posts;                              // all posts by this user
$post->user;                               // user who wrote this post
$user->posts()->latest()->paginate(10);    // chain query builder methods
```

## Many-to-many

Needs a **pivot table** — named with both model names in alphabetical order, singular: `post_tag`, not `tag_post`.

```bash
php artisan make:migration create_post_tag_table
```

```php
// Pivot migration
$table->foreignId('post_id')->constrained()->cascadeOnDelete();
$table->foreignId('tag_id')->constrained()->cascadeOnDelete();
```

**Both models get the same relationship type:**
```php
// Post
public function tags(): BelongsToMany
{
    return $this->belongsToMany(Tag::class);
}

// Tag
public function posts(): BelongsToMany
{
    return $this->belongsToMany(Post::class);
}
```

**Pivot operations:**
```php
$post->tags()->attach($tagId);       // add a tag
$post->tags()->detach($tagId);       // remove a tag
$post->tags()->sync([1, 2, 3]);      // replace all tags with these IDs
```

## Eager loading — always do this in controllers

```php
// ❌ N+1: one query per post in the loop
$posts = Post::all(); // then $post->user->name in Blade

// ✅ Two queries total regardless of result count
$posts = Post::with('user')->paginate(20);
$posts = Post::with(['user', 'tags'])->paginate(20);
```

📖 `laravel.com/docs/12.x/eloquent-relationships`
