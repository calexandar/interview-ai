---
paths:
  - 'app/**/*.php'
---

# App

## now() returns CarbonImmutable in this app
The app uses immutable dates, so `now()` returns Carbon\CarbonImmutable — do not type hints as Illuminate\Support\Carbon (TypeError at runtime, e.g. swallowed by Inertia defer rescue and surfaced only as rescuedProps). Use Carbon\CarbonInterface or CarbonImmutable for date params/returns.
