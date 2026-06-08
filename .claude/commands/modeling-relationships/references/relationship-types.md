# Eloquent Relationship Types — Full Reference

## Contents
- hasOne / belongsTo
- hasMany / belongsTo
- belongsToMany (many-to-many)
- Cascade delete options
- hasManyThrough (advanced)

---

## hasOne / belongsTo

**When:** One-to-one ownership — User hasOne Profile.

**Migration:** FK goes on the child table (profiles).
```php
$table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
// unique() because one user can only have one profile
```

**Parent (User):**
```php
public function profile(): HasOne
{
    return $this->hasOne(Profile::class);
}
```

**Child (Profile):**
```php
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

---

## hasMany / belongsTo

**When:** One-to-many — User hasMany Posts.

**Migration:** FK goes on the child table (posts).
```php
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

---

## belongsToMany

**When:** Many-to-many — Post belongsToMany Tags.

**Pivot table name:** both model names in alphabetical order, singular: `post_tag`.

**Pivot migration:**
```php
Schema::create('post_tag', function (Blueprint $table) {
    $table->foreignId('post_id')->constrained()->cascadeOnDelete();
    $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
    // no id() or timestamps() needed unless you use withTimestamps()
});
```

**Both models:**
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
$post->tags()->attach($tagId);             // add one tag
$post->tags()->attach([1, 2, 3]);          // add multiple
$post->tags()->detach($tagId);             // remove one
$post->tags()->detach();                   // remove all
$post->tags()->sync([1, 2, 3]);            // replace all with these IDs
$post->tags()->toggle([1, 2]);             // add if absent, remove if present
```

---

## Cascade delete options

```php
->constrained()->cascadeOnDelete()    // delete child when parent is deleted
->constrained()->nullOnDelete()       // set FK to null when parent is deleted
->constrained()->restrictOnDelete()   // block parent deletion if children exist (default behaviour)
```

**Choosing the right one:**
- `cascadeOnDelete` — child records are owned by the parent and have no meaning without it (e.g. post comments)
- `nullOnDelete` — child records can exist without a parent (e.g. posts by a deleted user, kept as "anonymous")
- `restrictOnDelete` — protect against accidental data loss (safer default when unsure)

---

## hasManyThrough (advanced — only when needed)

**When:** Accessing distant relationships. Country → has many Users → who have many Posts. Allows `$country->posts` directly.

```php
// Country model
public function posts(): HasManyThrough
{
    return $this->hasManyThrough(Post::class, User::class);
}
```

**Only use this when the direct relationship doesn't fit.** For most simple projects, this won't be needed.

📖 `laravel.com/docs/12.x/eloquent-relationships#has-many-through`
