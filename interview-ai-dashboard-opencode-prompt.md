# OpenCode Prompt --- Interview AI Dashboard

## 1. Objective

Create a complete, production-quality **Interview AI Dashboard** for an
AI-powered technical interviewing platform.

The dashboard is the primary authenticated interface for recruiters,
hiring managers, and administrators.

The dashboard should closely recreate the supplied reference image and
feel like a modern SaaS application:

-   Clean
-   Professional
-   AI-focused
-   Minimal
-   Data-driven
-   Enterprise-ready
-   Responsive
-   Accessible

Do not create a generic admin dashboard. The UI should specifically
communicate that the application is used to conduct and monitor
**AI-powered technical interviews**.

------------------------------------------------------------------------

## 2. Reference Image

Use the supplied dashboard screenshot as the primary visual reference.

The dashboard contains:

1.  Application sidebar
2.  Top navigation/header
3.  Active interview status
4.  Interview timer
5.  AI interviewer
6.  Interview question
7.  Audio waveform
8.  Recording controls
9.  Interview progress panel
10. Interview sections
11. Interview overview statistics
12. Interviews-over-time chart
13. Recent interviews
14. Skills assessment radar chart
15. AI agent status card
16. User profile section
17. Dark mode toggle
18. End Interview action

Match the visual hierarchy, spacing, typography, borders, shadows,
colors, and proportions from the reference.

------------------------------------------------------------------------

## 3. Technology Stack

Use the existing project stack:

-   Laravel 13
-   PHP 8.4+
-   Inertia.js 3
-   Vue 3
-   TypeScript
-   Tailwind CSS 4
-   Vite

Do not introduce another frontend framework or CSS framework.

Use the project's existing application layout and component architecture
where possible.

------------------------------------------------------------------------

## 4. Existing Architecture

The application follows a **vertical slice architecture based around
business capabilities**.

Do not organize the dashboard around generic technical categories such
as:

``` text
Controllers/
Services/
Repositories/
```

unless those folders already exist and are required by the project.

Prefer business-oriented features such as:

``` text
Interview
Candidate
Job
QuestionBank
Skill
Report
Dashboard
```

The dashboard should consume data from the appropriate business slices.

------------------------------------------------------------------------

## 5. Main Page

The dashboard page should be located approximately at:

``` text
resources/js/Pages/Dashboard/Index.vue
```

Adapt this path if the existing application uses a different page
structure.

The dashboard should use the existing authenticated application layout.

------------------------------------------------------------------------

## 6. Overall Layout

Use a three-part application structure:

``` text
┌─────────────────────────────────────────────────────────────┐
│                         TOP HEADER                           │
├───────────────┬─────────────────────────────┬───────────────┤
│               │                             │               │
│   SIDEBAR     │       MAIN CONTENT          │   OPTIONAL    │
│               │                             │   PANEL       │
│               │                             │               │
│               │                             │               │
│               │                             │               │
└───────────────┴─────────────────────────────┴───────────────┘
```

Desktop proportions:

-   Sidebar: approximately `236px`
-   Main: remaining width
-   Right interview progress panel: approximately `420px`

------------------------------------------------------------------------

## 7. Desktop Structure

``` text
Sidebar
│
├── Header
│
└── Main content
    │
    ├── Interview conductor
    │   ├── AI interviewer
    │   ├── Question card
    │   └── Interview progress
    │
    └── Dashboard widgets
        ├── Interview overview
        ├── Recent interviews
        └── Skills assessment
```

------------------------------------------------------------------------

## 8. Color Palette

Use a light interface.

### Primary

``` text
Primary Purple: #6D4AFF
Primary Purple Hover: #5E3DE5
Light Purple: #F1ECFF
Very Light Purple: #F8F6FF
```

### Status

``` text
Success: #22A06B
Success Background: #EAF8F1

Warning: #F5A623
Warning Background: #FFF6E5

Danger: #FF4D4F
Danger Background: #FFF1F1
```

### Text

``` text
Primary Text: #0F172A
Secondary Text: #475467
Muted Text: #667085
Very Muted: #98A2B3
```

### Borders and Backgrounds

``` text
Border: #E5E7EB
Application Background: #F8F7FC
Card Background: #FFFFFF
```

------------------------------------------------------------------------

## 9. Typography

Use the project's existing font.

If no font is configured, use:

``` text
Inter
```

Suggested sizing:

``` text
Page title:
20px / 24px
font-semibold

Section title:
16px
font-semibold

Body:
14px

Small text:
12px

Dashboard numbers:
20-24px
font-semibold
```

Avoid oversized typography. This is an application interface, not a
marketing landing page.

------------------------------------------------------------------------

## 10. Border Radius

Use a consistent radius system:

``` text
Small controls: 8px
Inputs: 10px
Cards: 14-16px
Large panels: 16-18px
Avatar: 50%
```

Do not use extremely rounded cards.

------------------------------------------------------------------------

## 11. Shadows

Use very subtle shadows.

Cards should primarily be separated using:

``` text
border + background
```

Recommended:

``` text
shadow-sm
```

Avoid heavy dashboard shadows.

------------------------------------------------------------------------

# 12. Application Header

Create:

``` text
TopHeader.vue
```

Suggested location:

``` text
resources/js/Components/Layout/TopHeader.vue
```

Header:

-   Spans the content area to the right of the sidebar
-   Height: approximately `64px`
-   White background
-   Bottom border

Use:

``` text
border-b border-gray-200
```

------------------------------------------------------------------------

## 13. Header --- Left Section

Display:

``` text
┌────┐
│ AI │  Interview AI  ›
└────┘
```

Use a purple square icon.

Brand:

``` text
Interview AI
```

Use `font-semibold`.

Add a small chevron.

------------------------------------------------------------------------

## 14. Header --- Interview Status

Next to the brand display:

``` text
Interview in Progress
● Live
```

The Live indicator should be green.

Use a small animated pulse for the Live dot.

Do not overanimate it.

------------------------------------------------------------------------

## 15. Header --- End Interview

Place the End Interview button near the center/right.

Button text:

``` text
End Interview
```

Style:

-   White background
-   Red border
-   Red text
-   Subtle hover state

Clicking it should open a confirmation modal.

------------------------------------------------------------------------

## 16. Header --- Timer

On the right side show:

``` text
◔
32:14
Time Remaining
```

Use a circular progress indicator.

The timer must be visually prominent but compact.

------------------------------------------------------------------------

## 17. Timer Component

Create:

``` text
InterviewTimer.vue
```

Responsibilities:

-   Display remaining time
-   Update every second
-   Display circular progress
-   Display low-time state
-   Trigger expiration state

Props should be conceptually similar to:

``` ts
duration
remaining
```

Do not place timer logic directly inside `Dashboard.vue`.

------------------------------------------------------------------------

## 18. Timer Behavior

Example:

``` text
30:00
29:59
29:58
...
```

When less than 5 minutes remain:

-   Use warning styling.

When less than 1 minute remains:

-   Use danger styling.

The frontend should not be the authoritative source of interview
duration. The backend remains the source of truth.

------------------------------------------------------------------------

## 19. Dark Mode

Add a moon icon on the far right.

Clicking it should toggle the existing application theme if dark mode is
supported.

Do not implement a completely separate dark theme if the application
does not already support one.

Use the existing theme system.

------------------------------------------------------------------------

# 20. Left Sidebar

Create:

``` text
Sidebar.vue
```

Suggested location:

``` text
resources/js/Components/Layout/Sidebar.vue
```

Width:

``` text
236px
```

Style:

-   White background
-   Right border
-   Full-height layout

------------------------------------------------------------------------

## 21. Sidebar Navigation

Navigation items:

``` text
Dashboard
Interviews
Candidates
Jobs
Question Bank
Skills
Reports
Settings
```

Use Lucide icons if available.

Suggested icons:

``` text
Dashboard:
LayoutDashboard

Interviews:
MessageSquare

Candidates:
Users

Jobs:
Briefcase

Question Bank:
BookOpen

Skills:
Network / Sparkles

Reports:
BarChart3

Settings:
Settings
```

------------------------------------------------------------------------

## 22. Active Sidebar Item

Dashboard should be active.

Style:

``` text
background: #F0EBFF
text: #6D4AFF
```

Use approximately `10px` border radius.

------------------------------------------------------------------------

## 23. Sidebar Navigation Behavior

Each navigation item must:

-   Be keyboard accessible
-   Have hover state
-   Have active state
-   Navigate to the appropriate route
-   Preserve existing Inertia navigation behavior

Do not use fake links.

Use actual application routes where available.

------------------------------------------------------------------------

## 24. Sidebar AI Agent Card

At the bottom of the navigation area create:

``` text
AI Agent
● Online

Your AI interviewer is ready to
conduct smart interviews.
```

Use an AI robot/avatar icon.

Card:

``` text
background: #F8F6FF
border-radius: 14px
```

Add a green Online indicator.

------------------------------------------------------------------------

## 25. AI Agent Avatar

Create a circular robot avatar.

The avatar can contain:

``` text
AI
```

or a small robot icon.

Use purple, white, and light-blue tones.

Do not use an external AI image unless the project already has one.

Prefer an icon/CSS-based avatar.

------------------------------------------------------------------------

## 26. Sidebar User Profile

At the bottom show:

``` text
Alex Cvetanovski
Admin
```

with:

-   Circular avatar
-   User name
-   Role
-   Dropdown chevron

Use the authenticated user's real name.

Do not hard-code the user name in production.

------------------------------------------------------------------------

# 27. Main Interview Area

The top portion of the dashboard is an active interview conductor.

Create:

``` text
InterviewConductor.vue
```

Suggested location:

``` text
resources/js/Features/Interview/Components/InterviewConductor.vue
```

The conductor contains:

``` text
AI Interviewer
+
Question / Recording area
+
Interview Progress
```

------------------------------------------------------------------------

## 28. Interview Conductor Background

Use a very subtle lavender background:

``` text
#F9F7FF
```

A very subtle radial gradient is acceptable.

Do not use a strong gradient.

------------------------------------------------------------------------

## 29. AI Interviewer Panel

Create:

``` text
AIInterviewerChat.vue
```

Display:

``` text
✦ AI Interviewer
```

Then a message card:

``` text
Welcome! I'm your AI interviewer. I'll be asking you
questions to assess your skills in web development.

Let's start with a question.
```

Timestamp:

``` text
10:30 AM
```

------------------------------------------------------------------------

## 30. AI Message Card

Card:

``` text
background: white
border: 1px solid #E7E7EF
border-radius: 12px
shadow-sm
```

Approximate width:

``` text
360px
```

AI icon should be purple.

------------------------------------------------------------------------

# 31. Question Card

The center of the interview conductor contains the active question.

Create:

``` text
QuestionCard.vue
```

Example:

``` text
Can you explain the difference between
let, const, and var in JavaScript?
```

Center-align the question.

Typography:

``` text
18-20px
font-semibold
```

------------------------------------------------------------------------

## 32. Question Card Layout

Use this visual hierarchy:

``` text
┌─────────────────────────────────────┐
│                                     │
│   Can you explain the difference    │
│   between let, const, and var in    │
│   JavaScript?                       │
│                                     │
│   ~~~~~ AUDIO WAVEFORM ~~~~~        │
│                                     │
│   Listening to your answer...       │
│                         ● 00:18     │
│                                     │
└─────────────────────────────────────┘
```

Card:

-   White background
-   Thin border
-   14px radius
-   Subtle shadow

------------------------------------------------------------------------

## 33. Audio Waveform

Create:

``` text
AudioWaveform.vue
```

The waveform should visually resemble:

``` text
▂▃▅▃▂▆▃▂▇▂▃▆▂▃▅▃▇▂▃▆▂
```

Use CSS or SVG.

Do not require a waveform library unless one already exists.

Use:

``` text
#7C4DFF
```

------------------------------------------------------------------------

## 34. Waveform Animation

When recording/listening:

-   Animate waveform.

When paused:

-   Make waveform static.

When recording:

``` text
Listening to your answer...
```

When processing:

``` text
Processing your answer...
```

When completed:

``` text
Answer recorded
```

------------------------------------------------------------------------

## 35. Recording Timer

Display:

``` text
● 00:18
```

The dot should be red.

The timer updates every second.

When recording is active, use a subtle red pulse.

------------------------------------------------------------------------

## 36. Recording Controls

Below the question card:

``` text
[ microphone ]
[ stop ]
```

Microphone button:

-   White
-   Border
-   Circular

Stop button:

-   Red
-   Circular
-   Square stop icon

------------------------------------------------------------------------

## 37. Recording Control Label

Below controls:

``` text
Click to stop recording
```

Use muted text.

------------------------------------------------------------------------

## 38. Audio Controls

At the bottom-left of the interview area:

``` text
Volume2
Settings
```

Use small icon buttons with accessible labels.

------------------------------------------------------------------------

# 39. Interview Progress Panel

Create:

``` text
ProgressPanel.vue
```

Suggested location:

``` text
resources/js/Features/Interview/Components/ProgressPanel.vue
```

Panel:

-   White
-   Full height within the interview area
-   Subtle border

------------------------------------------------------------------------

## 40. Progress Panel Tabs

Tabs:

``` text
Interview Progress
Notes
```

Active tab:

-   Purple text
-   Purple underline

------------------------------------------------------------------------

## 41. Progress Header

Display:

``` text
Senior Frontend Developer
```

Below:

-   Progress bar
-   Percentage

Example:

``` text
60%
```

------------------------------------------------------------------------

## 42. Progress Bar

Track:

``` text
#E6E4F0
```

Fill:

``` text
#7C4DFF
```

Use rounded corners.

Example:

``` text
██████████████████░░░░░░
60%
```

------------------------------------------------------------------------

## 43. Interview Sections

The progress timeline contains:

``` text
Introduction
Technical Skills
Problem Solving
System Design
Behavioral
Closing
```

------------------------------------------------------------------------

## 44. Introduction Section

Completed state:

-   Green circle
-   Check icon

Display:

``` text
5 / 5
Completed
```

------------------------------------------------------------------------

## 45. Technical Skills Section

Active state:

-   Purple circle
-   Light purple background

Display:

``` text
8 / 15
In Progress
```

------------------------------------------------------------------------

## 46. Technical Skill Subsections

Under Technical Skills:

``` text
Frontend (JavaScript)
Frameworks (Vue/React)
HTML & CSS
```

Progress:

``` text
Frontend:
3 / 5

Frameworks:
2 / 5

HTML & CSS:
0 / 5
```

Statuses:

``` text
In Progress
Pending
Pending
```

Use smaller typography.

------------------------------------------------------------------------

## 47. Remaining Sections

Display:

``` text
Problem Solving
0 / 10
Pending

System Design
0 / 5
Pending

Behavioral
0 / 5
Pending

Closing
0 / 5
Pending
```

Use empty circular indicators.

------------------------------------------------------------------------

## 48. Progress Timeline

Use a vertical line connecting sections:

``` text
● Introduction
│
● Technical Skills
│
├── Frontend
├── Frameworks
└── HTML & CSS
│
○ Problem Solving
│
○ System Design
│
○ Behavioral
│
○ Closing
```

States:

-   Completed: green
-   Active: purple
-   Pending: gray

------------------------------------------------------------------------

# 49. Dashboard Widgets Section

Below the interview conductor create three cards:

``` text
┌──────────────────┬──────────────────┬──────────────────┐
│ Interview        │ Recent           │ Skills           │
│ Overview         │ Interviews       │ Assessment       │
└──────────────────┴──────────────────┴──────────────────┘
```

Desktop:

``` text
grid-cols-3
```

Tablet:

``` text
grid-cols-2
```

Mobile:

``` text
grid-cols-1
```

------------------------------------------------------------------------

# 50. Interview Overview Card

Title:

``` text
Interview Overview
```

Dropdown:

``` text
This Week ˅
```

------------------------------------------------------------------------

## 51. Overview Statistics

Display four statistic cards:

``` text
12
Interviews
+20%

8
Completed
+33%

4
In Progress
+10%

85%
Avg. Score
+8%
```

------------------------------------------------------------------------

## 52. Statistic Styling

Use subtle accent colors.

Interviews:

-   Purple

Completed:

-   Green

In Progress:

-   Orange

Average Score:

-   Blue

Use very light tinted backgrounds.

Do not use saturated backgrounds.

------------------------------------------------------------------------

## 53. Interviews Over Time Chart

Title:

``` text
Interviews Over Time
```

Days:

``` text
Mon
Tue
Wed
Thu
Fri
Sat
Sun
```

Example values:

``` text
6
10
17
18
12
11
5
```

Use a purple line with subtle grid lines.

Use a chart library only if one already exists in the project. Otherwise
use SVG.

Do not add a large chart dependency only for this graph unless
necessary.

------------------------------------------------------------------------

# 54. Recent Interviews Card

Title:

``` text
Recent Interviews
```

Button:

``` text
View All
```

Use a small outlined button.

------------------------------------------------------------------------

## 55. Recent Interview List

### David Johnson

``` text
Senior Frontend Developer
86%
Today, 10:30 AM
In Progress
```

### Sarah Williams

``` text
Full Stack Developer
78%
Today, 09:15 AM
Completed
```

### Michael Brown

``` text
Laravel Developer
92%
Yesterday
Completed
```

### Emily Davis

``` text
Vue.js Developer
65%
Yesterday
Completed
```

### James Wilson

``` text
Backend Developer
70%
May 12, 2024
Completed
```

Use realistic data structures rather than hard-coding markup.

------------------------------------------------------------------------

## 56. Candidate Avatar

Use circular avatars.

If real candidate images are available, use them.

Otherwise use initials:

``` text
DJ
SW
MB
ED
JW
```

------------------------------------------------------------------------

## 57. Score Badges

Display:

``` text
86%
78%
92%
65%
70%
```

Use subtle background colors.

High scores:

-   Green

Medium:

-   Blue

Lower:

-   Orange

Do not rely on color alone to communicate meaning.

------------------------------------------------------------------------

## 58. Interview Status Badges

Statuses:

``` text
In Progress
Completed
```

In Progress:

-   Purple background
-   Purple text

Completed:

-   Green background
-   Green text

------------------------------------------------------------------------

# 59. Skills Assessment Card

Title:

``` text
Skills Assessment (Live)
```

Display a radar chart.

------------------------------------------------------------------------

## 60. Radar Chart

Skills:

``` text
JavaScript
Vue/React
HTML/CSS
Problem Solving
Communication
System Design
```

Example values:

``` text
JavaScript: 85
Vue/React: 80
HTML/CSS: 90
Problem Solving: 75
Communication: 70
System Design: 60
```

Use purple.

The polygon should have a very light purple fill.

------------------------------------------------------------------------

## 61. Radar Chart Footer

Display:

``` text
● Scores update in real-time as the interview progresses
```

Use a purple dot and muted text.

------------------------------------------------------------------------

## 62. Live Skill Updates

The radar chart must be designed so values can later come from the
backend.

Use a typed data structure such as:

``` ts
interface SkillScore {
    skill: string;
    score: number;
}
```

Do not hard-code the chart component to static values.

------------------------------------------------------------------------

# 63. Dashboard Data

The dashboard should eventually receive:

``` text
interview
candidate
job
interview progress
interview sections
statistics
recent interviews
skill scores
timer
AI agent status
```

Keep UI components presentational.

Do not put database queries inside Vue components.

------------------------------------------------------------------------

# 64. Suggested Component Structure

Use a structure similar to:

``` text
resources/js/
├── Components/
│   ├── Layout/
│   │   ├── Sidebar.vue
│   │   └── TopHeader.vue
│   │
│   └── Dashboard/
│       ├── InterviewOverviewCard.vue
│       ├── RecentInterviewsCard.vue
│       ├── SkillsAssessmentCard.vue
│       ├── StatCard.vue
│       └── InterviewsChart.vue
│
├── Features/
│   └── Interview/
│       ├── Components/
│       │   ├── InterviewConductor.vue
│       │   ├── AIInterviewerChat.vue
│       │   ├── QuestionCard.vue
│       │   ├── AudioWaveform.vue
│       │   ├── RecordingControls.vue
│       │   ├── InterviewTimer.vue
│       │   └── ProgressPanel.vue
│       │
│       ├── Types/
│       │   └── interview.ts
│       │
│       └── Composables/
│           └── useInterviewTimer.ts
│
├── Features/
│   └── Dashboard/
│       ├── Components/
│       │   ├── InterviewOverviewCard.vue
│       │   ├── RecentInterviewsCard.vue
│       │   ├── SkillsAssessmentCard.vue
│       │   └── InterviewsChart.vue
│       │
│       └── Types/
│           └── dashboard.ts
│
└── Pages/
    └── Dashboard/
        └── Index.vue
```

Adapt the structure to match the project's existing conventions.

------------------------------------------------------------------------

# 65. Business Slice Architecture

Because the application uses business-oriented vertical slices,
interview-specific components should preferably live under:

``` text
resources/js/Features/Interview/
```

For example:

``` text
resources/js/Features/Interview/
├── Components/
├── Types/
└── Composables/
```

Dashboard-specific components should live under:

``` text
resources/js/Features/Dashboard/
├── Components/
└── Types/
```

Do not mix unrelated business logic into the Dashboard feature.

------------------------------------------------------------------------

# 66. Backend Architecture

The backend should remain organized around business capabilities.

For example:

``` text
app/
├── Actions/
├── Domain/
│   ├── Interview/
│   ├── Candidate/
│   ├── Job/
│   ├── Skill/
│   └── Dashboard/
```

Do not create a generic `DashboardService` containing every piece of
business logic.

Prefer focused business actions such as:

``` text
GetInterviewDashboard
GetInterviewProgress
GetRecentInterviews
GetInterviewStatistics
GetSkillAssessment
```

Only create these if they fit the existing architecture.

------------------------------------------------------------------------

# 67. Dashboard Data Endpoint

The dashboard should receive structured data from Laravel through
Inertia.

Conceptually:

``` php
return Inertia::render('Dashboard/Index', [
    'interview' => ...,
    'progress' => ...,
    'statistics' => ...,
    'recentInterviews' => ...,
    'skills' => ...,
]);
```

Do not make multiple unnecessary API requests when the page can be
rendered through Inertia.

------------------------------------------------------------------------

# 68. TypeScript Types

Create typed interfaces.

Example:

``` ts
interface Interview {
    id: number;
    candidate: Candidate;
    job: Job;
    status: InterviewStatus;
    startedAt: string;
    remainingSeconds: number;
}

interface InterviewProgress {
    percentage: number;
    sections: InterviewSection[];
}

interface InterviewSection {
    name: string;
    completed: number;
    total: number;
    status: 'completed' | 'in_progress' | 'pending';
}
```

Add types for:

-   Statistics
-   Recent interviews
-   Skills
-   AI agent status
-   Timer

Avoid `any`.

------------------------------------------------------------------------

# 69. Loading States

Implement skeleton states for:

-   Interview conductor
-   Progress panel
-   Overview card
-   Recent interviews
-   Skills chart

Use subtle `animate-pulse` placeholders.

------------------------------------------------------------------------

# 70. Empty States

Create sensible empty states.

Examples:

``` text
No interviews yet.
Start your first AI interview.
```

Recent interviews:

``` text
No recent interviews.
```

Skills:

``` text
Skills assessment will appear once
the interview begins.
```

------------------------------------------------------------------------

# 71. Error States

Handle failed data loading.

Display:

``` text
Something went wrong.
We couldn't load the interview dashboard.
Try again.
```

Do not expose exceptions or stack traces.

------------------------------------------------------------------------

# 72. Interview End Confirmation

When clicking:

``` text
End Interview
```

show a modal:

``` text
End this interview?

Are you sure you want to end the interview?
The candidate's current progress will be saved.

Cancel
End Interview
```

The destructive action should be red.

The modal must:

-   Trap focus
-   Support Escape
-   Support keyboard navigation
-   Close on Cancel
-   Confirm through the correct backend action

------------------------------------------------------------------------

# 73. Recording State

Clearly communicate recording state.

### Active

``` text
red recording indicator
animated waveform
recording timer
```

### Paused

``` text
neutral indicator
```

### Processing

``` text
Processing answer...
```

------------------------------------------------------------------------

# 74. Interview State

Support:

``` text
not_started
in_progress
paused
processing
completed
expired
```

Use typed enums/types.

Do not scatter string literals throughout the UI.

------------------------------------------------------------------------

# 75. Question State

Questions can be:

``` text
pending
asking
listening
processing
answered
```

The UI should be prepared to display these states.

------------------------------------------------------------------------

# 76. AI Interviewer State

AI Agent status:

``` text
Online
Thinking
Speaking
Listening
Processing
Offline
```

Use appropriate visual indicators.

Current reference state:

``` text
Online
```

------------------------------------------------------------------------

# 77. Responsive Design

## Desktop

Use:

``` text
Sidebar
+
Interview conductor
+
Progress panel
```

## Tablet

Move the progress panel below the interview conductor:

``` text
Interview conductor
↓
Progress panel
↓
Dashboard widgets
```

## Mobile

-   Sidebar becomes a drawer
-   Header becomes compact
-   Interview conductor becomes one column
-   Progress panel becomes collapsible
-   Dashboard cards become one column

------------------------------------------------------------------------

# 78. Mobile Sidebar

The sidebar should become a drawer.

Use a menu button in the mobile header.

When opened:

``` text
overlay
+
sidebar
```

Clicking the overlay closes it.

------------------------------------------------------------------------

# 79. Mobile Interview Layout

On mobile:

``` text
AI Interviewer
↓
Question
↓
Waveform
↓
Recording controls
↓
Progress
```

The question must remain readable.

Do not shrink typography excessively.

------------------------------------------------------------------------

# 80. Mobile Dashboard Cards

Stack:

``` text
Interview Overview

Recent Interviews

Skills Assessment
```

Each card should be full width.

------------------------------------------------------------------------

# 81. Accessibility

Use:

-   Semantic HTML
-   Proper buttons
-   Keyboard navigation
-   Visible focus states
-   ARIA labels where needed
-   Accessible chart descriptions
-   Accessible modal
-   Accessible sidebar drawer
-   Accessible tabs
-   Accessible timer information

Do not rely exclusively on color.

For example:

``` text
green check icon + Completed
```

instead of only green.

------------------------------------------------------------------------

# 82. Charts Accessibility

Charts should have accessible labels.

Example:

``` text
aria-label="Interviews over time"
```

Radar chart:

``` text
aria-label="Live skills assessment"
```

Provide textual information where appropriate.

------------------------------------------------------------------------

# 83. Animations

Use subtle animations for:

-   Live indicator pulse
-   Waveform animation
-   Progress updates
-   Chart updates
-   Hover transitions
-   Modal transitions
-   Sidebar drawer transition

Avoid excessive animation.

------------------------------------------------------------------------

# 84. Real-Time Architecture Preparation

The dashboard should be designed so interview progress can later be
updated in real time.

Potential future architecture:

``` text
Laravel
    ↓
Events
    ↓
Broadcasting
    ↓
WebSocket
    ↓
Vue
```

Potential events:

``` text
InterviewProgressUpdated
SkillScoreUpdated
InterviewTimerUpdated
AIResponseGenerated
InterviewCompleted
```

Do not implement WebSockets unless they already exist in the project.

Structure components so they can accept reactive props/state later.

------------------------------------------------------------------------

# 85. AI Interview Interaction

The central question card should be ready for future AI integration.

Conceptual flow:

``` text
AI asks question
       ↓
Candidate speaks
       ↓
Audio captured
       ↓
Speech-to-text
       ↓
AI evaluates answer
       ↓
Skill score updated
       ↓
Next question
```

Do not implement the AI engine in this dashboard UI task.

Only implement the visual interface and appropriate states.

------------------------------------------------------------------------

# 86. Do Not Put AI Logic in Vue

Do not implement:

``` text
OpenAI API
Anthropic API
LLM prompts
candidate evaluation
speech-to-text
audio processing
```

inside Vue components.

Those belong to the backend/domain layer.

The dashboard should consume results.

------------------------------------------------------------------------

# 87. Performance

Avoid:

-   Excessive watchers
-   Unnecessary API requests
-   Large image assets
-   Heavy animation libraries
-   Large chart libraries if SVG is sufficient

Use computed properties where appropriate.

Keep components focused.

------------------------------------------------------------------------

# 88. Code Quality

Use:

-   TypeScript
-   Strict typing
-   Small components
-   Reusable components
-   Composition API

Avoid:

``` text
any
huge components
duplicated markup
inline business logic
hardcoded routes
```

------------------------------------------------------------------------

# 89. Final Page Hierarchy

``` text
Application
│
├── Sidebar
│   ├── Brand
│   ├── Navigation
│   ├── AI Agent
│   └── User Profile
│
├── Header
│   ├── Interview identity
│   ├── Live status
│   ├── End Interview
│   ├── Timer
│   └── Theme
│
└── Main
    │
    ├── Active Interview
    │   ├── AI Interviewer
    │   ├── Question
    │   ├── Waveform
    │   ├── Recording Controls
    │   └── Interview Progress
    │
    └── Dashboard Widgets
        ├── Interview Overview
        │   ├── Statistics
        │   └── Line Chart
        │
        ├── Recent Interviews
        │   └── Interview List
        │
        └── Skills Assessment
            └── Radar Chart
```

------------------------------------------------------------------------

# 90. Acceptance Criteria

The implementation is complete when:

-   [ ] Dashboard visually matches the reference image.
-   [ ] Sidebar is implemented.
-   [ ] Top header is implemented.
-   [ ] Interview in Progress state is visible.
-   [ ] Live indicator is implemented.
-   [ ] End Interview button works.
-   [ ] End Interview confirmation modal works.
-   [ ] Interview timer is implemented.
-   [ ] Timer updates every second.
-   [ ] Timer warning states exist.
-   [ ] Dark mode control integrates with the existing theme system.
-   [ ] AI Agent card is implemented.
-   [ ] User profile is implemented.
-   [ ] AI interviewer message is implemented.
-   [ ] Interview question card is implemented.
-   [ ] Audio waveform is implemented.
-   [ ] Waveform animation exists for recording state.
-   [ ] Recording timer is implemented.
-   [ ] Recording controls are implemented.
-   [ ] Interview progress panel is implemented.
-   [ ] Progress tabs are implemented.
-   [ ] Progress percentage is displayed.
-   [ ] Interview section timeline is implemented.
-   [ ] Completed sections are visually distinct.
-   [ ] Active section is visually distinct.
-   [ ] Pending sections are visually distinct.
-   [ ] Technical Skills subsections are implemented.
-   [ ] Interview Overview card is implemented.
-   [ ] Statistics are implemented.
-   [ ] Interviews-over-time chart is implemented.
-   [ ] Recent Interviews card is implemented.
-   [ ] Candidate avatars are implemented.
-   [ ] Interview scores are implemented.
-   [ ] Interview status badges are implemented.
-   [ ] Skills Assessment card is implemented.
-   [ ] Radar chart is implemented.
-   [ ] Live score messaging is implemented.
-   [ ] Loading states exist.
-   [ ] Empty states exist.
-   [ ] Error states exist.
-   [ ] Responsive desktop layout works.
-   [ ] Responsive tablet layout works.
-   [ ] Responsive mobile layout works.
-   [ ] Mobile sidebar drawer works.
-   [ ] Components are reusable.
-   [ ] TypeScript types are used.
-   [ ] No unnecessary dependencies are added.
-   [ ] No business logic is placed inside presentation components.
-   [ ] Existing Laravel authentication remains intact.
-   [ ] Existing Inertia architecture is respected.
-   [ ] Existing vertical slice architecture is respected.
-   [ ] Accessibility requirements are satisfied.

------------------------------------------------------------------------

# 91. Important Implementation Instruction

Do **not** simply create a static HTML mockup.

Build the dashboard as a real Vue/Inertia application interface.

The UI must be prepared for real data coming from Laravel.

Use realistic mock data only where backend data is not yet available.

The dashboard must be:

-   Maintainable
-   Componentized
-   Responsive
-   Accessible
-   Type-safe
-   Ready for real-time interview functionality

The most important visual elements are:

1.  Left navigation
2.  Interview header
3.  AI interviewer
4.  Question + waveform
5.  Recording controls
6.  Interview progress timeline
7.  Overview statistics
8.  Interviews chart
9.  Recent interviews
10. Live skills radar
11. AI agent card
12. User profile
13. Timer
14. End Interview action

Recreate all of these elements before adding any additional UI.

The final result should look like a polished commercial product called:

# Interview AI

An AI-powered platform for conducting, evaluating, and managing
technical interviews.
