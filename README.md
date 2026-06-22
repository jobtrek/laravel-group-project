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

### 6. With Docker (Laravel Sail)

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
