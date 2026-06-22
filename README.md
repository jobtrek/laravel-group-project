# Laravel Group Project

A Laravel 13 web application built with [Laravel Sail](https://laravel.com/docs/sail) and PostgreSQL.

## Requirements

- [Docker](https://www.docker.com/) and [Docker Compose](https://docs.docker.com/compose/)
- Or: PHP 8.3+, Composer, and PostgreSQL (for local development without Docker)

## Getting Started

### 1. Clone the Repository
```bash
git clone <repository-url>
cd laravel-group-project
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Install Git Hooks
```bash
./vendor/bin/captainhook install
```

This sets up the pre-commit hook that automatically formats your PHP code before every commit — no manual step needed after this.

### 4. Install Node Modules
```bash 
./vendor/bin/sail npm install

```


### 5. Set Up Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 6. Configure Mail (Mailtrap)

The project sends automated reminder emails to project leaders. For local development it uses [Mailtrap](https://mailtrap.io) — a free sandbox that catches all outgoing mail without delivering it.

1. Create a free account at [mailtrap.io](https://mailtrap.io)
2. Go to **Email Testing → your inbox → SMTP Settings → Laravel**
3. Copy the `MAIL_USERNAME` and `MAIL_PASSWORD` values into your `.env`

Your `.env` mail block should look like:
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=<your-mailtrap-username>
MAIL_PASSWORD=<your-mailtrap-password>
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 7. With Docker (Laravel Sail)

I highly recommend creating an alias in your shell configuration for easier access to Sail commands:

```bash 
alias sail='./vendor/bin/sail'
```

Start the development environment:
```bash
./vendor/bin/sail up -d
```

Run migrations:
```bash
./vendor/bin/sail artisan migrate
```

The application will be available at `http://localhost`.


to run the frontend build:
```bash
./vendor/bin/sail npm run dev
```


## Development

### Available Commands

```bash
# Start development server with Sail
./vendor/bin/sail up

# Run artisan commands
./vendor/bin/sail artisan <command>

# Access the database
./vendor/bin/sail psql

# Run tests
./vendor/bin/sail test

# Format code with Pint
./vendor/bin/sail pint

# Run queue listener
./vendor/bin/sail artisan queue:listen
```

### Mail & Scheduled Reminders

The project automatically emails project leaders when their projects go stale. Two Artisan commands handle this, scheduled to run via Laravel's task scheduler:

| Command | Schedule | Purpose |
|---|---|---|
| `mail:send-reminders` | Monday 09:00 | Friendly nudge to leaders of projects not updated in a month |
| `mail:send-warnings` | Wednesday 09:00 | Firm warning to all members of projects overdue after a reminder |

Emails are dispatched as queued jobs, so **both the queue worker and the scheduler must be running** for mail to actually send.

```bash
# Keep the queue worker running (processes queued mail jobs)
./vendor/bin/sail artisan queue:listen

# Run the scheduler (for local dev — checks every minute and fires due commands)
./vendor/bin/sail artisan schedule:work
```

To trigger the commands manually without waiting for the schedule:
```bash
./vendor/bin/sail artisan mail:send-reminders
./vendor/bin/sail artisan mail:send-warnings
```

All sent mail is caught by Mailtrap — check your inbox at [mailtrap.io](https://mailtrap.io) to see it.

### Code Formatting

[Laravel Pint](https://laravel.com/docs/pint) is used for code style. The pre-commit hook (installed in step 3) runs automatically on every `git commit` — it formats any staged PHP files and re-stages them, so your commits always go out clean.

To format manually at any time:
```bash
# Format all files
./vendor/bin/sail pint

# Format only staged/modified files (same as the hook does)
./vendor/bin/sail pint --dirty
```

If a colleague hasn't run `./vendor/bin/captainhook install` yet, they can always format manually before committing.

### Database

The project uses PostgreSQL as configured in `.env`. Key settings:
- **Host**: `pgsql` (Sail) or `localhost` (local)
- **Port**: `5432`
- **Database**: `laravel`
- **User**: `sail`
- **Password**: `password`

## Project Structure

- `app/` - Application code (controllers, models, etc.)
- `database/` - Migrations, factories, and seeders
- `resources/` - Views, CSS, and JavaScript
- `routes/` - API and web routes
- `tests/` - Test suites

## Documentation

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sail Documentation](https://laravel.com/docs/sail)
- [Eloquent ORM](https://laravel.com/docs/eloquent)

## License

This project is licensed under the MIT License.
