---
name: running-artisan
description: Quick reference for PHP Artisan make commands used in Laravel development — generating models, controllers, migrations, form requests, policies, jobs, mail, and events. Use when the user asks what artisan command to run, how to generate a file, needs the syntax for a make command, or wants to know what flags are available.
---

# Artisan Command Reference

See [references/commands.md](references/commands.md) for the full list of `make:` commands with all flags.

## Most used — generate everything at once

```bash
# Model + migration + resource controller + form requests
php artisan make:model Post -mcrR

# Or piece by piece
php artisan make:model Post
php artisan make:migration create_posts_table
php artisan make:controller PostController --resource
php artisan make:request StorePostRequest
php artisan make:policy PostPolicy --model=Post
```

## Database

```bash
php artisan migrate                  # run pending migrations
php artisan migrate:rollback         # undo last batch
php artisan migrate:status           # see which migrations have run
php artisan migrate:fresh            # drop everything and re-run (destroys data)
php artisan migrate:fresh --seed     # fresh + run seeders
php artisan db:seed                  # run seeders without re-migrating
```

## Diagnosing problems

```bash
php artisan route:list               # all registered routes
php artisan route:list --name=posts  # filter by name
php artisan config:clear             # clear cached config
php artisan cache:clear              # clear app cache
php artisan view:clear               # clear compiled Blade views
php artisan tinker                   # interactive PHP REPL
```

## Running inside Sail

Prefix `php artisan` with `./vendor/bin/sail`:

```bash
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan tinker
./vendor/bin/sail artisan make:model Post -m
```

Add `alias sail='./vendor/bin/sail'` to your shell config (`~/.zshrc`) to shorten this to just `sail artisan`.
