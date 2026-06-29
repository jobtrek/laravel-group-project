# Sail & Docker Checks (🔵)

Only relevant if `docker-compose.yml` is present in the project.

---

## 19. PostgreSQL Port Exposed to `0.0.0.0`

```yaml
# ❌ Accessible from outside the container on any interface
ports:
    - '5432:5432'

# ✅ Bind to localhost only
ports:
    - '127.0.0.1:5432:5432'
```

Also check: is `FORWARD_DB_PORT` in `.env` actually needed? Remove it if not.

---

## 20. Weak Credentials in Sail `.env`

```env
DB_CONNECTION=pgsql
DB_HOST=pgsql        # correct for Sail (Docker service name, not localhost)
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password  # ❌ change this, even locally
```

For any non-local environment: strong unique `DB_PASSWORD`, `APP_KEY` rotated, `APP_ENV=production`.

---

## 21. Sail in Production

**Sail is not intended for production.** If the project will be deployed:

- Strip `docker-compose.yml` / Sail from the production image
- Use a proper Docker setup (Dokploy, Forge, Railway) with a hardened Dockerfile
- Confirm `laravel/sail` is in `require-dev` in `composer.json`, not `require`
