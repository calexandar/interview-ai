Objective

Design and implement the first UI version of an AI-powered technical interviewing platform.

The product allows companies to create technical positions, invite candidates, conduct AI-powered interviews, evaluate answers, and review structured candidate assessments.

The UI should feel like a modern premium SaaS product, not like a generic chatbot or traditional ATS.

The visual direction should be:

Clean
Professional
Modern
Minimal
Technical
Human
Trustworthy
AI-native without being overly futuristic

Use the previously generated UI concept as visual inspiration: soft pastel backgrounds, rounded cards, subtle shadows, purple/indigo accents, generous whitespace, and clear information hierarchy.

1. Technology

Use:

Laravel 13
PHP 8.4
Inertia.js 3
Vue 3
TypeScript
Tailwind CSS 4
Filament 5 for the internal/admin interface

The candidate-facing interface should be implemented using:

Inertia.js
Vue 3
TypeScript
Tailwind CSS 4

Do not implement the candidate interface with Filament.

2. Product Name

Use a temporary product name:

Skillora

Tagline:

AI-powered technical interviews

The branding must be easy to replace later.

Do not hard-code the product name throughout the application.

Create a simple reusable brand component.

3. Design Language
Colors

Use a light interface as the primary design.

Suggested palette:

Background:
#F7F7FB


Surface:
#FFFFFF


Primary:
#6D5DF5


Primary dark:
#5647D9


Text:
#17171C


Secondary text:
#6B6B76


Border:
#E8E8EF


Success:
#22A06B


Warning:
#E5A11A


Danger:
#D64545

Do not overuse purple.

Purple should primarily identify:

AI functionality
Primary actions
Active navigation
Important status
Interview progress

The interface should still feel mostly neutral.

4. Typography

Use a modern sans-serif font.

Preferred:

Inter

or a similar clean system font.

Typography hierarchy:

Page title:
32px / semibold


Section title:
20-24px / semibold


Card title:
16-18px / semibold


Body:
14-16px


Metadata:
12-13px

Keep typography highly readable.

Avoid excessive font weights.

5. General UI Principles

Use:

Rounded cards
12–20px border radius
Subtle borders
Very subtle shadows
Generous spacing
Clear hierarchy
Consistent button styles
Consistent form controls

Avoid:

Heavy gradients
Excessive glassmorphism
Neon colors
Excessive animations
Huge text
Dense enterprise dashboards
Excessive borders
Generic Bootstrap-like UI

The product should feel closer to:

Linear
+
Notion
+
Modern AI SaaS

than a traditional HR application.

6. Application Structure

The application has two primary experiences.

Recruiter / Company Application

Used by:

HR
Recruiters
Hiring managers

Main navigation:

Dashboard


Positions
Candidates
Interviews
Assessments


Question Bank


Settings
Candidate Experience

The candidate should not see the recruiter navigation.

The candidate experience should be focused entirely on the interview.

7. Recruiter Dashboard

Create:

/resources/js/Pages/Dashboard.vue

The dashboard should contain:

Header
Good morning, Alex


Here's what's happening with your hiring pipeline.

Primary action:

+ Create Position
Statistics

Four cards:

Active Positions
12


Candidates
184


Interviews
46


Strong Candidates
18

Each card should contain:

Icon
Number
Label
Small trend indicator

Example:

┌────────────────────────────┐
│ Active Positions       ↗   │
│                            │
│ 12                         │
│ +3 this month              │
└────────────────────────────┘
8. Recent Interviews

Create a card:

Recent Interviews

Columns:

Candidate
Position
Date
Score
Recommendation
Status

Example:

John Smith
Senior Laravel Developer
Today
8.4 / 10
Strong Hire
Completed

Use small avatar initials instead of large profile images.

9. Position Page

Create:

/resources/js/Pages/Positions/Index.vue

The page should display positions as cards or a clean table.

Each position:

Senior Laravel Developer


Senior
Technical Interview


Skills:
PHP
Laravel
SQL
Architecture


12 candidates


[View Position]

Primary action:

+ Create Position
10. Create Position

Create:

/resources/js/Pages/Positions/Create.vue

Use a multi-step form.

Step 1 — Basic Information
Position title
Description
Experience level
Step 2 — Skills

Allow the recruiter to add skills.

Example:

Required skills


Laravel       ● Required
PHP           ● Required
SQL           ● Required
Testing       ● Required
Vue.js        ○ Optional

Each skill can have:

Importance
Required / Optional
Step 3 — Interview Configuration
Interview type
Technical


Duration
45 minutes


Number of questions
12


Difficulty
Adaptive
Step 4 — Review

Show a summary before creating the position.

11. Candidate List

Create:

/resources/js/Pages/Candidates/Index.vue

Use a clean table.

Columns:

Candidate
Position
Interview
Score
Recommendation
Status

Include:

Search
Position filter
Interview status filter
Score filter

Do not make the page visually dense.

12. Candidate Detail

Create:

/resources/js/Pages/Candidates/Show.vue

Header:

John Smith


Senior Laravel Developer


john@example.com

Show:

Interview history
Skills
Assessments
Recent activity

Main card:

Latest Assessment


8.4 / 10


Strong Hire
13. Interview Detail / Recruiter View

Create:

/resources/js/Pages/Interviews/Show.vue

Display:

Candidate
John Smith


Position
Senior Laravel Developer


Status
Completed


Score
8.4 / 10

Then:

Skill Breakdown
Laravel          9.0
PHP              8.5
Architecture     8.4
SQL              7.1
Testing          6.3

Use horizontal progress bars.

14. Interview Transcript

Show the complete interview.

Use a conversation-style layout:

AI Interviewer


How would you structure a Laravel application
with multiple implementations of the same
interface?


Candidate


I would use the service container and bind
different implementations depending on the
context...

Each question should have:

Question
Answer
Score

But don't make the UI look like a normal ChatGPT conversation.

The interview should feel like a professional assessment.

15. Candidate Interview Experience

This is the most important screen.

Create:

/resources/js/Pages/Interviews/Conduct.vue

The screen should be distraction-free.

Layout:

┌─────────────────────────────────────────────────────┐
│ Skillora                                      04:32 │
├─────────────────────────────────────────────────────┤
│                                                     │
│              Technical Interview                   │
│                                                     │
│              Question 5 of 12                      │
│              ────────────────                      │
│                                                     │
│  How would you design a Laravel application        │
│  that needs to process 100,000 jobs per hour?      │
│                                                     │
│                                                     │
│  ┌─────────────────────────────────────────────┐    │
│  │                                             │    │
│  │  Write your answer...                      │    │
│  │                                             │    │
│  │                                             │    │
│  └─────────────────────────────────────────────┘    │
│                                                     │
│                     [ Submit Answer ]               │
│                                                     │
│              You can take your time.               │
│                                                     │
└─────────────────────────────────────────────────────┘
16. Candidate Interview Header

Keep the header minimal.

Left:

Skillora

Center:

Senior Laravel Developer

Right:

Question 5 / 12

Optionally show a subtle timer.

Do not show the candidate's score during the interview.

17. AI Question Card

The question should be the primary visual focus.

Use a large clean card.

Example:

Question 5


Architecture


How would you design a Laravel application
that needs to process 100,000 jobs per hour?

Show the skill subtly:

Architecture · Advanced

Do not expose internal AI information.

18. Answer Input

Use a large textarea.

Features:

Auto-resize
Character count
Keyboard shortcut hint
Clear focus state
Disabled state
Loading state

Placeholder:

Explain your approach and include any
trade-offs you would consider...

Primary button:

Submit Answer →
19. Answer Submission State

After clicking submit:

Analyzing your answer...

Show a subtle animated AI indicator.

Example:

✦ Analyzing your response

Do not show fake progress.

Do not display:

87%

unless there is a real progress value.

20. Next Question Transition

After evaluation completes, transition smoothly to the next question.

Use a subtle:

fade + slide

animation.

Duration:

200–300ms

Avoid excessive animation.

21. Interview Completion Screen

After the final question:

Interview complete


Thank you, John.


Your interview has been submitted successfully.


The hiring team will review your results.

Do not show the candidate's internal AI recommendation unless the product explicitly supports candidate feedback.

Primary action:

Close Interview
22. Assessment Results

Create:

/resources/js/Pages/Assessments/Show.vue

This should be the main recruiter-facing results page.

Hero section:

John Smith


Senior Laravel Developer


Strong Hire


8.4 / 10

Use a large score indicator.

23. Skill Radar / Visualization

Create a visual competency section.

Skills:

PHP
Laravel
SQL
Testing
Architecture
APIs

A radar chart can be used if it remains visually clean.

Otherwise use horizontal bars.

Prefer simple visualization over complex dashboards.

24. Strengths

Create a card:

Strengths


✓ Strong Laravel architecture knowledge


✓ Excellent understanding of dependency injection


✓ Strong practical problem-solving ability
25. Concerns

Create:

Areas to Explore


! Testing depth is below the expected senior level


! Limited experience with advanced SQL optimization

Use neutral language.

Avoid making the AI sound overly authoritative.

26. Interview Summary

Create:

Interview Summary

Example:

The candidate demonstrated strong practical Laravel
experience and a solid understanding of application
architecture. They performed particularly well in
dependency injection, queues, and API design.


Testing and advanced SQL optimization were weaker
areas and may warrant additional discussion during
the next interview stage.
27. Recommendation

Create a prominent but restrained recommendation card:

AI Recommendation


STRONG HIRE


Confidence
87%


Based on:
12 questions
8 assessed skills
45 minute interview

Clearly label it as:

AI Recommendation

The UI should make clear that this is an AI-generated assessment, not an objective hiring decision.

28. Responsive Design

The application must work well on:

Desktop
Laptop
Tablet
Mobile

The candidate interview experience is particularly important on smaller screens.

On mobile:

Header
Question
Answer input
Submit button

should stack naturally.

The answer input should remain comfortable to use.

29. Accessibility

Follow WCAG-friendly practices.

Implement:

Proper labels
Keyboard navigation
Visible focus states
Accessible buttons
Semantic HTML
Sufficient contrast
Screen-reader-friendly status messages
aria-live for AI processing states

Do not rely on color alone for status.

30. Loading / Empty / Error States

Every major page should have:

Loading

Skeleton UI rather than blank screens.

Empty

Example:

No interviews yet


Start your first candidate interview
to see results here.


[Create Interview]
Error

Example:

Something went wrong


We couldn't load this interview.


[Try Again]
31. Component Architecture

Create reusable Vue components.

Suggested:

resources/js/Components/
├── Brand/
├── Buttons/
├── Cards/
├── Forms/
├── Navigation/
├── Status/
├── Scores/
├── Skills/
└── Interviews/

Interview-specific components:

InterviewHeader.vue
InterviewProgress.vue
QuestionCard.vue
AnswerEditor.vue
AnswerSubmissionState.vue
InterviewComplete.vue
SkillScore.vue
AssessmentSummary.vue
RecommendationBadge.vue

Do not create huge Vue components.

For example, avoid:

ConductInterview.vue

containing 1,000+ lines.

32. Design System

Create reusable UI primitives for:

Button
Card
Badge
Input
Textarea
Select
Modal
Dropdown
Progress
Avatar
Tabs
EmptyState
Skeleton

Use Tailwind utility classes.

Avoid creating a custom component for every tiny element.

33. Icons

Use a consistent icon library.

Prefer:

Lucide

Icons should be subtle and consistent.

Avoid mixing multiple icon styles.

34. Animations

Use animation only where it improves the experience.

Good:

Question transition
Button hover
Card hover
AI processing indicator
Progress update
Toast

Avoid:

Constant floating elements
Large animated backgrounds
Excessive gradients
Attention-grabbing animations
35. AI Visual Language

AI functionality should have a recognizable but subtle visual language.

Use:

✦

or a small sparkle icon for AI-generated content.

For example:

✦ AI Recommendation

Do not use robots, brains, glowing circuits, or generic futuristic imagery.

The product should feel like a professional hiring platform powered by AI, not an AI demo.

36. Dark Mode

Prepare the component architecture for dark mode.

Do not make dark mode the priority for this milestone.

The light theme should be fully polished first.

37. UX Priorities

Prioritize these in order:

1. Candidate can understand the question immediately.


2. Candidate can easily submit an answer.


3. Candidate always knows where they are in the interview.


4. Recruiter can understand the assessment quickly.


5. Recruiter can drill into individual answers.


6. AI-generated information is clearly identified.


7. The interface feels trustworthy.
38. Important Product Principle

The UI should communicate:

The AI assists the hiring process. It does not replace human judgment.

Avoid language such as:

"This candidate is definitely a bad hire."

Prefer:

"AI Recommendation: No Hire"


"Areas that may require further evaluation..."

This distinction should be reflected throughout the product.

39. Implementation Order

Build the UI in this order:

1. Application shell
   ↓
2. Dashboard
   ↓
3. Position list
   ↓
4. Create Position
   ↓
5. Candidate list
   ↓
6. Interview setup
   ↓
7. Candidate interview screen
   ↓
8. Interview loading/transition states
   ↓
9. Interview completion
   ↓
10. Assessment results
   ↓
11. Interview transcript

The candidate interview screen and assessment results screen are the highest-priority screens.

40. Important Constraints

Do not:

Add voice functionality.
Add video.
Add WebSockets unless required.
Add a complex design system library.
Add unnecessary npm dependencies.
Build billing.
Build CV parsing.
Build a complete ATS.
Add fake data that conflicts with the backend models.
Put business logic inside Vue components.
Put AI provider calls inside controllers.
Put database queries directly inside Vue components.

Keep business logic in the Laravel vertical slices.

41. Backend Boundary

The UI should consume the business use cases through Inertia.

For example:

Vue
 ↓
POST /interviews/{interview}/answers
 ↓
SubmitAnswer
 ↓
SubmitAnswerHandler
 ↓
Queue evaluation

The Vue component should not know:

OpenAI
Anthropic
AI prompts
Scoring algorithms
Database structure

It only knows:

Question
Answer
Progress
Submission state
Next question
42. Final Visual Goal

The finished interface should look like a premium AI hiring SaaS.

Think:

Linear
+
Notion
+
Modern AI products
+
Professional recruiting software

with:

Soft neutral background
White cards
Purple AI accents
Rounded corners
Subtle shadows
Excellent typography
Generous whitespace
Clear data visualization
Minimal navigation

The most important screen should feel calm and focused:

The candidate should feel like they are having a structured professional technical interview — not chatting with a chatbot.

The recruiter should feel:

"I can understand this candidate's technical ability in under 30 seconds."

Implement the UI with clean reusable Vue components and keep the code aligned with the project's