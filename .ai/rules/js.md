---
paths:
  - 'resources/js/**'
---

# Js

## Regenerate Wayfinder with --with-form
Running `php artisan wayfinder:generate` without flags regenerates resources/js/routes and actions WITHOUT .form helpers, breaking types:check (Property 'form' does not exist) in pages using route().form. Always run `php artisan wayfinder:generate --with-form`. Generated files are gitignored; the vite plugin regenerates them on dev/build.
