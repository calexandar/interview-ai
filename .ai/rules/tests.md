---
paths:
  - 'tests/**/*.php'
---

# Tests

## Tests must run against MySQL, not phpunit.xml's sqlite
pdo_sqlite is not installed on this machine, so `php artisan test` fails with "could not find driver". Create the test DB once (`mysql -h127.0.0.1 -uroot -p... -e "CREATE DATABASE IF NOT EXISTS interview_ai_test;"`) and run tests with `DB_CONNECTION=mysql DB_DATABASE=interview_ai_test php artisan test --compact`. Also: stale Vite manifests break Inertia component() assertions after adding/renaming pages — run `npm run build` first.
