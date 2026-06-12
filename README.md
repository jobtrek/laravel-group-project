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
### 3. Install Node Modules
```bash 
./vendor/bin/sail npm install

```


### 3. Set Up Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. With Docker (Laravel Sail)

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
./vendor/bin/sail pgsql

# Run tests
./vendor/bin/sail test

# Format code with Pint
./vendor/bin/sail pint

# Run queue listener
./vendor/bin/sail artisan queue:listen
```

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
