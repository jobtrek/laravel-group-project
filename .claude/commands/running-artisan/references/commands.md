# Artisan Make Commands — Full Reference

## Contents
- Models and database
- Controllers
- Validation and auth
- Background jobs and mail
- Events
- Other

---

## Models and database

```bash
php artisan make:model Post
php artisan make:model Post -m           # + migration
php artisan make:model Post -mc          # + migration + controller
php artisan make:model Post -mcr         # + migration + resource controller
php artisan make:model Post -mcrR        # + migration + resource controller + form requests
php artisan make:model Post --all        # migration + seeder + factory + controller + policy + form requests

php artisan make:migration create_posts_table
php artisan make:migration add_slug_to_posts_table    # adds up/down for altering a table
php artisan make:migration create_post_tag_table      # pivot table

php artisan make:seeder PostSeeder
php artisan make:factory PostFactory --model=Post
```

---

## Controllers

```bash
php artisan make:controller PostController
php artisan make:controller PostController --resource   # index, create, store, show, edit, update, destroy
php artisan make:controller PostController --invokable  # single __invoke method
php artisan make:controller Admin/PostController        # nested under app/Http/Controllers/Admin/
```

---

## Validation and auth

```bash
php artisan make:request StorePostRequest
php artisan make:request UpdatePostRequest

php artisan make:policy PostPolicy
php artisan make:policy PostPolicy --model=Post        # pre-fills view/create/update/delete methods

php artisan make:middleware EnsureUserIsVerified
```

---

## Background jobs and mail

```bash
php artisan make:job SendWelcomeEmail

php artisan make:mail OrderConfirmation
php artisan make:mail OrderConfirmation --markdown=emails.orders.confirmation

php artisan make:notification InvoicePaid
```

---

## Events

```bash
php artisan make:event UserRegistered
php artisan make:listener SendWelcomeEmail --event=UserRegistered
php artisan make:observer PostObserver --model=Post
```

---

## Other

```bash
php artisan make:component Alert                # Blade component (app/View/Components + resources/views/components)
php artisan make:rule Uppercase                 # custom validation rule
php artisan make:command SendDailyReport        # custom artisan command

php artisan list                                # see all available commands
php artisan help make:model                     # see all flags for a specific command
```
