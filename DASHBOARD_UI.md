# OpenCode Prompt — Interview AI Dashboard

## 1. Objective

Create a complete, production-quality **Interview AI Dashboard** for an AI-powered technical interviewing platform.

The dashboard is the primary authenticated interface for recruiters, hiring managers, and administrators.

The implementation must closely recreate the supplied reference image.

The dashboard should feel like a modern SaaS application:

- Clean
- Professional
- AI-focused
- Minimal
- Data-driven
- Enterprise-ready
- Responsive
- Accessible

Do not create a generic admin dashboard.

The UI should specifically communicate that the application is used to conduct and monitor **AI-powered technical interviews**.

---

# 2. Reference Image

Use the supplied dashboard screenshot as the primary visual reference.

The page contains:

1. Application sidebar
2. Top navigation/header
3. Active interview status
4. Interview timer
5. AI interviewer
6. Interview question
7. Audio waveform
8. Recording controls
9. Interview progress panel
10. Interview sections
11. Interview overview statistics
12. Interviews-over-time chart
13. Recent interviews
14. Skills assessment radar chart
15. AI agent status card
16. User profile section
17. Dark mode toggle
18. End Interview action

Match the visual hierarchy, spacing, typography, borders, shadows, colors, and proportions from the reference.

---

# 3. Technology Stack

Use the existing project stack:

- Laravel 13
- PHP 8.4+
- Inertia.js 3
- Vue 3
- TypeScript
- Tailwind CSS 4
- Vite

Do not introduce another frontend framework.

Do not introduce another CSS framework.

Use the project's existing application layout and component architecture where possible.

---

# 4. Existing Architecture

The application follows a **vertical slice architecture based around business capabilities**.

Do not organize the dashboard implementation around generic technical categories such as:

```text
Controllers/
Services/
Repositories/