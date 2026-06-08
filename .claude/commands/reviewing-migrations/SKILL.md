---
name: reviewing-migrations
description: Checks a Laravel migration for common mistakes before it is run — null safety on existing tables, missing indexes, rollback correctness, foreign key constraints, and column naming conventions. Use when the user asks to write, review, or check a migration, or when making any database schema change.
---

# Reviewing Laravel Migrations

Work through this checklist before running any migration.

---

## 1. New column on an existing table — nullable or has a default

```php
// ❌ Fails if the table has existing rows — NOT NULL with no default
$table->string('phone');

// ✅ Option A: allow null
$table->string('phone')->nullable();

// ✅ Option B: provide a default
$table->boolean('is_verified')->default(false);
```

**Rule:** Any new column added to an existing table must be `->nullable()` or have a `->default()`.

---

## 2. Foreign keys use `foreignId()->constrained()`

```php
// ❌ Manual — verbose and easy to get wrong
$table->unsignedBigInteger('user_id');
$table->foreign('user_id')->references('id')->on('users');

// ✅ One line — creates the column, index, and FK constraint
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
```

---

## 3. Index columns you filter or sort by

```php
// Add an index if you'll use this column in ->where() or ->orderBy()
$table->string('status')->index();

// Compound index when you filter by two columns together
$table->index(['user_id', 'status']);
```

`foreignId()->constrained()` already adds an index — no need to add one manually for FK columns.

---

## 4. The `down()` method actually reverses `up()`

```php
public function up(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->string('slug')->nullable();
    });
}

// ❌ Empty down() — can never roll back
public function down(): void {}

// ✅ Reverses exactly what up() did
public function down(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->dropColumn('slug');
    });
}
```

---

## 5. Column naming conventions

```php
// ❌
$table->string('firstName');
$table->string('UserEmail');

// ✅ snake_case throughout
$table->string('first_name');
$table->string('email');
```

- **Booleans:** prefix with `is_` or `has_` — `is_active`, `has_verified_email`
- **Timestamps:** past-tense verb — `published_at`, `verified_at`, `deleted_at`
- **Soft deletes:** use `$table->softDeletes()` (adds `deleted_at`) + `use SoftDeletes` on the model

---

## 6. Migration file order — referenced table must exist first

If `posts` has a FK to `users`, the `create_users_table` migration must have an earlier filename prefix. Laravel runs migrations in alphabetical filename order (`2024_01_01_000000` → earliest runs first).

---

## Quick self-check before running

- [ ] New columns on existing tables are nullable or have a default?
- [ ] Foreign keys use `foreignId()->constrained()`?
- [ ] Frequently-queried columns have an index?
- [ ] `down()` reverses `up()` correctly?
- [ ] Column names are snake_case?
- [ ] Dependencies (referenced tables) exist in earlier migrations?
