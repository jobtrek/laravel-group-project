# Commit Message Examples

## feat — new functionality
```
feat: add user registration form
feat: add password reset via email
feat: add pagination to posts index
```

## fix — bug fixes
```
fix: redirect to dashboard after login instead of /home
fix: prevent duplicate form submissions on slow connections
fix: correct typo in validation error message
```

## docs
```
docs: add setup instructions to README
docs: document environment variables in .env.example
```

## refactor
```
refactor: extract order total calculation into OrderService
refactor: replace raw DB queries with Eloquent scopes
```

## chore / ci
```
chore: update Laravel to 12.x
ci: add PHPUnit step to pipeline
```

## What makes a bad commit message

| Bad | Why |
|-----|-----|
| `fix stuff` | Too vague — what was fixed? |
| `WIP` | Commits to a PR should be complete units of work |
| `fixed the thing with the users` | No type prefix, still unclear |
| `feat: Added new feature for users to be able to login to the system using their credentials` | Too long, past tense, no scope |
