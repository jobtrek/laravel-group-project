# Update Log — Issue #229

## What changed

- `phpunit.xml`: added `DB_CONNECTION=sqlite` and set `DB_DATABASE=:memory:` in the `<php>` env block, so the test suite runs against an in-memory SQLite database instead of inheriting whatever `DB_CONNECTION` is set in `.env` (normally `pgsql`, host `pgsql`, for Sail).
- `tests/Feature/MailtrapConnectionTest.php`: removed the `'smtp connection to mailtrap is reachable'` test, which opened a real SMTP connection to Mailtrap during the run. The other tests in that file already used `Mail::fake()` and needed no changes.

## Why

Outside Docker/Sail, `DB_CONNECTION` is undefined or points at `pgsql`, so `composer test` failed on ~47/48 tests with "could not find driver" — the pgsql PDO driver isn't installed on a bare host. `phpunit.xml` never pinned the connection to SQLite, even though the project's own conventions (`CLAUDE.md`) say tests should use SQLite. Separately, the Mailtrap SMTP test made the suite depend on network access to an external service, which is unrelated to a unit/feature test run and can fail or hang with no local network/credentials.

## Result

After the fix, `composer test` runs fully offline with no pgsql/network dependency: 49 tests execute (22 passing). The remaining 16 failures and 11 errors are pre-existing application issues unrelated to this fix (e.g. a missing `manage everything` permission seed, `UserFactory::unverified()` not defined, `proposer_id` not set by a factory, and a missing Vite-built asset for `logo-white.svg`) — out of scope for #229.
