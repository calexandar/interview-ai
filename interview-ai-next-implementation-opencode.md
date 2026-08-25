# OpenCode Implementation Prompt — Interview AI Backend & Interview Flow

## Objective

The Interview AI dashboard UI has already been implemented.

The next task is to turn the existing UI into a functional interview platform, starting with the **Interview domain and deterministic interview flow**.

**Do not start with the LLM, speech-to-text, text-to-speech, or WebSocket implementation.**

The first milestone must be a complete interview flow using predefined questions.

The system must be architected so AI capabilities can be added later without redesigning the Interview domain.

---

# 1. Current Project Context

The application is an AI-powered platform for conducting technical interviews.

The current frontend already contains:

- Dashboard
- Interview in Progress UI
- AI Interviewer panel
- Interview question card
- Recording controls
- Interview timer
- Interview progress panel
- Interview sections
- Interview overview
- Recent interviews
- Skills assessment
- User profile
- AI agent status

The UI should now be connected to real Laravel data.

Do not replace or redesign the existing UI unless something is necessary for functionality.

---

# 2. Technology Stack

Use the existing project stack:

- Laravel 13
- PHP 8.4+
- Inertia.js 3
- Vue 3
- TypeScript
- Tailwind CSS 4
- MySQL/PostgreSQL according to the existing project configuration

Do not introduce another framework.

Do not add unnecessary dependencies.

---

# 3. Architecture

The application uses a **vertical slice architecture based on business capabilities**.

Do not organize business logic around generic technical layers such as:

```text
Services/
Repositories/
Managers/
Helpers/
```

The main business slices should be:

```text
Interview
Candidate
Job
QuestionBank
Skill
AI
Report
Dashboard
```

The Interview domain is the first domain to implement.

---

# 4. Recommended Backend Structure

Use a structure similar to:

```text
app/
├── Domain/
│   ├── Interview/
│   │   ├── Actions/
│   │   ├── Data/
│   │   ├── Enums/
│   │   ├── Events/
│   │   ├── Models/
│   │   └── Queries/
│   │
│   ├── Candidate/
│   ├── Job/
│   ├── QuestionBank/
│   ├── Skill/
│   ├── AI/
│   │   ├── Actions/
│   │   ├── Data/
│   │   ├── Prompts/
│   │   └── Services/
│   │
│   └── Report/
│
└── Http/
    ├── Controllers/
    └── Requests/
```

Adapt this to the existing project structure instead of blindly creating duplicate architecture.

---

# 5. Important Architectural Rule

The Interview domain must own the interview lifecycle.

The AI layer must **not** become responsible for controlling interview state.

The architecture should be:

```text
Interview Domain
       │
       ├── controls interview state
       ├── controls questions
       ├── controls answers
       ├── controls progression
       ├── controls timing
       └── controls completion
                │
                ↓
              AI
       ┌────────┴────────┐
       │                 │
 Generate             Evaluate
 Question             Answer
```

The LLM is an intelligence provider.

It is not the source of truth for application state.

---

# 6. First Milestone

Implement this complete flow:

```text
Create Interview
      ↓
Start Interview
      ↓
Load First Section
      ↓
Load First Question
      ↓
Display Question
      ↓
Candidate Provides Answer
      ↓
Save Answer
      ↓
Mark Question Answered
      ↓
Update Progress
      ↓
Load Next Question
      ↓
...
      ↓
Complete Interview
```

For this milestone:

**Use predefined questions.**

Do not generate questions with AI yet.

---

# 7. Interview Domain

Create the core Interview domain representing:

```text
Interview
Interview Section
Interview Question
Interview Answer
Interview Skill Score
```

Do not create unnecessary models until they are actually required.

---

# 8. Interview Model

Conceptual fields:

```text
id
candidate_id
job_id
status
started_at
completed_at
paused_at
duration_seconds
created_at
updated_at
```

Additional fields can be added if the existing project requires them.

Use foreign keys for candidate and job.

Do not duplicate candidate/job information inside the interview without a strong reason.

---

# 9. Interview Status

Create an enum similar to:

```php
enum InterviewStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case InProgress = 'in_progress';
    case Paused = 'paused';
    case Completed = 'completed';
    case Expired = 'expired';
    case Cancelled = 'cancelled';
}
```

Use the enum throughout the backend. Avoid scattered raw status strings.

---

# 10. Interview Sections

An interview consists of sections such as:

```text
Introduction
Technical Skills
Problem Solving
System Design
Behavioral
Closing
```

Each interview should be able to have its own section configuration.

Conceptual fields:

```text
id
interview_id
name
slug
order
status
started_at
completed_at
```

Prefer calculating question counts from relationships rather than maintaining duplicate counters without a reason.

---

# 11. Section Status

Create:

```php
enum InterviewSectionStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
}
```

The Interview domain controls section state.

---

# 12. Interview Questions

Questions belong to an interview section.

Conceptual fields:

```text
id
interview_section_id
question_bank_id nullable
question
skill_id nullable
difficulty
order
status
asked_at
answered_at
```

`question_bank_id` may be nullable because questions can later be AI-generated.

---

# 13. Question Status

Create:

```php
enum InterviewQuestionStatus: string
{
    case Pending = 'pending';
    case Asking = 'asking';
    case Answering = 'answering';
    case Processing = 'processing';
    case Answered = 'answered';
    case Skipped = 'skipped';
}
```

The first implementation mainly needs:

```text
pending
asking
answering
answered
skipped
```

Keep the other states for future AI/audio processing.

---

# 14. Interview Answers

Create an InterviewAnswer model.

Conceptual fields:

```text
id
interview_question_id
answer
answered_at
duration_seconds
score nullable
evaluation nullable
```

The first implementation should support text answers.

Do not require audio yet.

Later the model should be able to support:

```text
transcript
audio_path
AI evaluation
score
```

---

# 15. Relationships

Expected relationships:

```text
Interview
    belongsTo Candidate
    belongsTo Job
    hasMany InterviewSection

InterviewSection
    belongsTo Interview
    hasMany InterviewQuestion

InterviewQuestion
    belongsTo InterviewSection
    hasMany InterviewAnswer
```

Reuse existing models where they already exist.

---

# 16. Database Migrations

Create only missing migrations for:

```text
interviews
interview_sections
interview_questions
interview_answers
```

Before creating migrations:

1. Inspect existing models.
2. Inspect existing migrations.
3. Inspect the current database structure.
4. Reuse existing concepts.
5. Avoid duplicate tables.

Use foreign keys and appropriate indexes.

Deleting an interview must never accidentally delete its candidate or job.

---

# 17. Question Bank

The QuestionBank domain should provide reusable technical questions.

Conceptually:

```text
Question
    skill
    difficulty
    type
    question
    evaluation_criteria
```

Seed realistic questions for:

```text
JavaScript
Vue
React
Laravel
PHP
HTML/CSS
Databases
System Design
```

The first milestone only needs enough QuestionBank functionality to create deterministic interviews.

Do not build a complete administration UI unless one already exists.

---

# 18. Seed Initial Questions

Create a development seeder with realistic questions such as:

```text
JavaScript
- What is the difference between let, const and var?
- Explain closures in JavaScript.
- How does the JavaScript event loop work?

Vue
- Explain Vue's reactivity system.
- What is the difference between computed and watch?

Laravel
- Explain Laravel middleware.
- How does dependency injection work in Laravel?
- How would you optimize an N+1 query problem?
```

Each question should have:

```text
skill
difficulty
question
evaluation criteria
```

---

# 19. Interview Actions

Use focused business actions rather than a giant InterviewService.

Recommended actions:

```text
CreateInterview
StartInterview
GetCurrentQuestion
SubmitAnswer
MoveToNextQuestion
SkipQuestion
PauseInterview
ResumeInterview
CompleteInterview
```

Keep each action focused on one business operation.

---

# 20. StartInterview

Create:

```text
StartInterview.php
```

Responsibilities:

1. Verify the interview exists.
2. Verify authorization.
3. Verify the interview can be started.
4. Set status to `in_progress`.
5. Set `started_at`.
6. Activate the first section.
7. Activate the first question.
8. Return the updated interview state.

Do not allow completed/cancelled interviews to start again.

Expected initial state:

```text
Interview = in_progress
First Section = in_progress
First Question = asking
Everything else = pending
```

---

# 21. Get Current Question

Create a focused query/action for retrieving the active question.

Conceptually:

```text
GetCurrentQuestion
```

It should resolve:

```text
Interview
  ↓
Active Section
  ↓
Active Question
```

Do not duplicate this logic across controllers.

---

# 22. SubmitAnswer

Create:

```text
SubmitAnswer.php
```

Input:

```text
Interview
Question
Answer
Duration
```

Responsibilities:

1. Validate interview state.
2. Validate question belongs to interview.
3. Validate question is currently answerable.
4. Create InterviewAnswer.
5. Mark question as answered.
6. Store answer timestamp.
7. Update progress.
8. Return updated interview state.

---

# 23. Prevent Duplicate Answers

The answer endpoint must be safe against:

- Double clicks
- Browser retries
- Network retries
- Multiple submissions

Use appropriate application checks, database constraints, transactions, or idempotency where needed.

---

# 24. MoveToNextQuestion

Responsibilities:

1. Find the next unanswered question.
2. If found, mark it `asking`.
3. If the current section is finished, complete it.
4. Activate the next section if one exists.
5. Activate the first question in the next section.
6. If all sections are complete, complete the interview.

Expected flow:

```text
Question 1 answered
      ↓
Question 2 asking
```

Section boundary:

```text
Last question answered
      ↓
Current section completed
      ↓
Next section in_progress
      ↓
First question asking
```

Final boundary:

```text
Last question answered
      ↓
Last section completed
      ↓
Interview completed
```

---

# 25. SkipQuestion

Implement:

```text
SkipQuestion.php
```

A skipped question should move to:

```text
skipped
```

Then the interview should progress to the next question.

Define and test a clear business rule for whether skipped questions count toward interview completion.

---

# 26. Pause and Resume

Implement:

```text
PauseInterview.php
ResumeInterview.php
```

Flow:

```text
in_progress → paused → in_progress
```

Record pause/resume timestamps as appropriate.

The current question must remain recoverable after resume.

---

# 27. CompleteInterview

Create:

```text
CompleteInterview.php
```

Responsibilities:

1. Verify the interview can be completed.
2. Complete the final section.
3. Set status to `completed`.
4. Set `completed_at`.
5. Ensure progress is consistent.
6. Dispatch an `InterviewCompleted` event if appropriate.

Do not calculate AI scores yet.

---

# 28. Progress Calculation

Calculate progress from real question data.

Example:

```text
Total questions: 30
Answered: 18

Progress: 60%
```

The frontend should receive:

```text
percentage
answered
total
```

Section progress should similarly be calculated from actual questions.

---

# 29. Interview Timer

Connect the existing timer UI to:

```text
interview.duration_seconds
interview.started_at
```

The browser may display the countdown, but the backend is authoritative.

Do not trust frontend timer values for interview validity.

The backend must recalculate expiration when important operations occur.

---

# 30. Interview Expiration

When the duration expires:

```text
in_progress → expired
```

After expiration:

- Do not accept normal answers.
- Do not start new questions.
- Do not allow normal interview progression.

The backend must enforce this even if the frontend still displays an active UI.

---

# 31. Controllers

Keep controllers thin.

Controllers should:

- Receive request
- Authorize
- Validate input
- Call business action
- Return Inertia/redirect/response

Do not put interview state transitions directly inside controllers.

Example:

```php
final class StartInterviewController
{
    public function __invoke(
        Interview $interview,
        StartInterview $action,
    ) {
        $interview = $action->handle($interview);

        return redirect()->route(
            'interviews.conduct',
            $interview,
        );
    }
}
```

Adapt to existing project conventions.

---

# 32. Routes

Create routes according to existing conventions.

Conceptually:

```text
GET  /interviews/{interview}/conduct
POST /interviews/{interview}/start
POST /interviews/{interview}/answer
POST /interviews/{interview}/next
POST /interviews/{interview}/skip
POST /interviews/{interview}/pause
POST /interviews/{interview}/resume
POST /interviews/{interview}/complete
```

Use named routes.

Do not hard-code URLs inside Vue components.

---

# 33. Inertia Integration

Connect the existing interview UI to real Laravel data.

Conceptually:

```php
return Inertia::render('Interview/Conduct', [
    'interview' => ...,
    'candidate' => ...,
    'job' => ...,
    'currentQuestion' => ...,
    'progress' => ...,
    'sections' => ...,
    'timer' => ...,
]);
```

Adapt this to the existing page/component structure.

Do not redesign the UI.

---

# 34. TypeScript Types

Create strong frontend types.

Example:

```ts
type InterviewStatus =
    | 'draft'
    | 'scheduled'
    | 'in_progress'
    | 'paused'
    | 'completed'
    | 'expired'
    | 'cancelled';

type QuestionStatus =
    | 'pending'
    | 'asking'
    | 'answering'
    | 'processing'
    | 'answered'
    | 'skipped';
```

Create interfaces for:

```text
Interview
Candidate
Job
InterviewSection
InterviewQuestion
InterviewAnswer
InterviewProgress
```

Avoid `any`.

---

# 35. Connect Existing UI

Replace mock values in the existing UI with real data.

Examples:

```text
Senior Frontend Developer
```

should come from:

```ts
interview.job.title
```

Progress:

```ts
progress.percentage
```

Section:

```ts
section.answered / section.total
```

Do not hard-code candidate, job, progress, or question values.

Do not change the visual design unless required.

---

# 36. End Interview

Connect the existing `End Interview` button to the backend.

Flow:

```text
Click
 ↓
Confirmation modal
 ↓
POST complete
 ↓
CompleteInterview
 ↓
Redirect to interview result
```

Do not simply change the frontend state.

---

# 37. Authorization

Authorization is critical.

A user must not be able to:

- View another company's interview.
- Submit answers to another company's interview.
- Modify another company's interview.
- Access another company's candidate data.

Use existing Laravel policies and tenant authorization.

Do not rely on hidden UI controls for security.

---

# 38. Multi-Tenant Awareness

If the application already has companies/organizations, respect the existing architecture.

Conceptually:

```text
Company
   │
   ├── Users
   ├── Candidates
   ├── Jobs
   └── Interviews
```

Every Interview operation must respect the current company context.

Do not introduce a second tenancy system.

---

# 39. Transactions

Use database transactions for state-changing operations where appropriate.

For example, `SubmitAnswer` should atomically perform:

```text
create answer
+
mark question answered
+
update interview/section state
```

If the operation fails, state must roll back.

---

# 40. Concurrency

Protect against:

```text
Candidate clicks Submit twice.
Candidate submits while interview expires.
Two browser tabs submit different answers.
```

Use appropriate transactions, row locking, unique constraints, or other mechanisms.

Do not over-engineer, but do not leave interview state vulnerable to race conditions.

---

# 41. Browser Refresh

The interview must survive a browser refresh.

After refresh:

```text
GET /interviews/{interview}/conduct
```

must reconstruct:

```text
current section
current question
progress
timer
interview status
```

Do not rely solely on Vue state.

---

# 42. Error Handling

If answer submission fails, display a useful message such as:

```text
Your answer could not be saved.
Please try again.
```

Do not silently discard candidate input.

Do not expose stack traces to users.

---

# 43. Events

Prepare domain events such as:

```text
InterviewStarted
QuestionAnswered
InterviewSectionCompleted
InterviewCompleted
InterviewPaused
InterviewResumed
```

Events are useful because AI evaluation, notifications, analytics, and reports can later subscribe without tightly coupling those concerns to Interview actions.

Do not implement unnecessary listeners yet.

---

# 44. Dashboard Data

Replace mock dashboard data with real values where the domain supports them.

Calculate:

```text
Total Interviews
Completed
In Progress
```

Do not display fake scores.

If scoring has not been implemented yet, show an appropriate empty/unavailable state instead of invented percentages.

---

# 45. Recent Interviews

Replace mock interview rows with real records.

Display:

```text
Candidate
Job
Score if available
Started/completed time
Status
```

If score does not exist:

```text
—
```

Do not generate fake scores.

---

# 46. Skills Assessment

Do not generate fake skill scores.

Until evaluation exists, display a suitable state such as:

```text
Skills assessment will appear once the interview is evaluated.
```

The existing radar chart should be prepared to consume real skill data later.

---

# 47. Seed Development Data

Create enough development data to test the complete flow:

```text
1 company
1 user
2-3 candidates
2 jobs
multiple skills
multiple question bank questions
1 active interview
```

The active interview should have multiple sections and enough questions to test:

- Multiple questions
- Section completion
- Progress calculation
- Interview completion

Reuse existing factories/seeders when available.

---

# 48. Testing Strategy

Write tests for the Interview domain.

At minimum:

```text
StartInterviewTest
SubmitAnswerTest
MoveToNextQuestionTest
SkipQuestionTest
PauseInterviewTest
ResumeInterviewTest
CompleteInterviewTest
InterviewAuthorizationTest
InterviewExpirationTest
```

---

# 49. Start Interview Tests

Test that:

- Draft/scheduled interviews can start when allowed.
- Completed interviews cannot start.
- Cancelled interviews cannot start.
- `started_at` is set.
- Status becomes `in_progress`.
- First section becomes active.
- First question becomes active.
- Remaining sections/questions remain pending.

---

# 50. Submit Answer Tests

Test that:

- Valid answers are stored.
- Questions become answered.
- Answer timestamps are stored.
- Progress changes.
- Invalid questions are rejected.
- Questions from another interview are rejected.
- Answers after completion are rejected.
- Answers after expiration are rejected.
- Duplicate submissions are handled safely.

---

# 51. Progression Tests

Test:

```text
Question 1 answered
        ↓
Question 2 active
```

Also:

```text
Last question of section
        ↓
Current section completed
        ↓
Next section active
```

And:

```text
Last question of final section
        ↓
Interview completed
```

---

# 52. Authorization Tests

Verify users cannot access or modify:

```text
another company's interview
another company's candidate
another user's interview
```

Use the application's existing authorization and tenancy mechanisms.

---

# 53. Frontend Integration

Update the existing Vue interview page to:

1. Display the current question from Inertia props.
2. Submit answers to Laravel.
3. Update/refresh interview state after successful operations.
4. Display real progress.
5. Display section status.
6. Display timer data.
7. Handle loading states.
8. Handle validation errors.
9. Handle completion.
10. Handle expiration.

Use the project's existing Inertia form conventions.

Do not add a new form library unless necessary.

---

# 54. Loading and Submission States

When submitting an answer, clearly show:

```text
Submitting...
```

Do not optimistically mark the answer as saved before backend confirmation.

Correctness is more important than perceived speed for an interview system.

---

# 55. Future AI Architecture

Do not implement AI in this milestone.

Prepare for the following architecture:

```text
Interview
     │
     ├── StartInterview
     ├── SubmitAnswer
     └── MoveToNextQuestion
             │
             ↓
          AI layer
             │
       ┌─────┴─────┐
       ↓           ↓
 Generate       Evaluate
 Question       Answer
```

The Interview domain should remain the authority over state.

---

# 56. Future AI Question Generation

Later create something similar to:

```text
app/Domain/AI/Actions/GenerateInterviewQuestion.php
```

Input:

```text
Job
Candidate
Skills
Current section
Previous questions
Previous answers
Difficulty
```

Output:

```text
question
skill
difficulty
evaluation criteria
```

Keep provider-specific logic outside the Interview model.

---

# 57. Future AI Answer Evaluation

Later create:

```text
EvaluateInterviewAnswer
```

Input:

```text
Question
Evaluation criteria
Candidate answer
Job requirements
Candidate context
```

Output:

```text
score
confidence
strengths
weaknesses
missing concepts
skill scores
feedback
```

Do not implement this yet.

---

# 58. Future AI Provider Abstraction

When AI is introduced, prefer an abstraction such as:

```php
interface InterviewAI
{
    public function generateQuestion(
        InterviewContext $context
    ): GeneratedQuestion;

    public function evaluateAnswer(
        EvaluationContext $context
    ): AnswerEvaluation;
}
```

This allows the application to change providers without redesigning the Interview domain.

Do not implement multiple providers now.

---

# 59. Future Voice Architecture

Do not implement voice in this milestone.

The future flow should be:

```text
AI question
      ↓
Text-to-Speech
      ↓
Candidate hears question
      ↓
Microphone
      ↓
Audio
      ↓
Speech-to-Text
      ↓
Transcript
      ↓
Answer evaluation
```

The current recording UI is preparation for this future capability.

---

# 60. What NOT to Implement Now

Do not implement:

- LLM question generation
- LLM answer evaluation
- Speech-to-text
- Text-to-speech
- WebSockets
- Broadcasting
- Real-time AI scoring
- Complex audio storage
- AI provider abstractions beyond what is needed for future compatibility
- A new frontend framework
- A new tenancy system

The first milestone is strictly the deterministic interview flow.

---

# 61. Implementation Sequence

Follow this sequence.

## Phase 1 — Inspect Existing Project

Before modifying code:

1. Inspect existing models.
2. Inspect migrations.
3. Inspect authentication.
4. Inspect company/tenant architecture.
5. Inspect existing Job/Candidate/Skill functionality.
6. Inspect existing routes.
7. Inspect existing Inertia pages.
8. Inspect existing vertical slice conventions.
9. Inspect existing tests and factories.

Do not create duplicate functionality.

---

## Phase 2 — Interview Database

Implement missing:

```text
interviews
interview_sections
interview_questions
interview_answers
```

Add:

- migrations
- relationships
- enums
- factories
- seeders

---

## Phase 3 — Interview Actions

Implement:

```text
CreateInterview
StartInterview
GetCurrentQuestion
SubmitAnswer
MoveToNextQuestion
SkipQuestion
PauseInterview
ResumeInterview
CompleteInterview
```

Use focused business actions.

---

## Phase 4 — Routes and Controllers

Implement the interview lifecycle endpoints.

Keep controllers thin.

---

## Phase 5 — Inertia Integration

Connect the existing UI to real backend data.

Replace mock values without redesigning the UI.

---

## Phase 6 — Tests

Implement the complete Interview domain test suite.

Run:

```bash
php artisan test
```

Fix all failures before considering the milestone complete.

---

## Phase 7 — Seed Realistic Development Data

Make it possible to run the complete interview flow locally with seeded data.

---

# 62. Definition of Done

This milestone is complete when the following works end-to-end:

```text
Recruiter creates interview
        ↓
Candidate/interview exists
        ↓
Interview starts
        ↓
First section becomes active
        ↓
First question appears
        ↓
Candidate enters answer
        ↓
Answer is saved
        ↓
Question becomes answered
        ↓
Progress updates
        ↓
Next question appears
        ↓
Section completes
        ↓
Next section starts
        ↓
All questions complete
        ↓
Interview becomes completed
```

Additionally:

- Browser refresh preserves state.
- Unauthorized users cannot access interviews.
- Expired interviews cannot accept answers.
- Duplicate answer submission is handled safely.
- Existing UI displays real data.
- Tests pass.
- No fake scores are displayed.

---

# 63. Final Architecture

The resulting architecture should approximately be:

```text
                    Interview AI
                         │
             ┌───────────┴───────────┐
             │                       │
         Interview                 AI
             │                       │
     ┌───────┼────────┐       ┌──────┴──────┐
     │       │        │       │             │
 Candidate   Job   QuestionBank   Generate   Evaluate
     │       │        │          Question   Answer
     │       │        │
     └───────┴────────┘
             │
         Interview
             │
      ┌──────┼──────────┐
      │      │          │
   Sections Questions Answers
      │      │          │
      └──────┴──────────┘
             │
          Progress
             │
          Reports
```

The most important architectural boundary is:

```text
Interview owns state.
AI provides intelligence.
```

---

# 64. Next Milestone

After this deterministic interview flow is complete and tested, implement the text-based AI interviewer:

```text
Interview Context
      ↓
Generate Question
      ↓
Candidate Answer
      ↓
Evaluate Answer
      ↓
Update Skill Score
      ↓
Generate Follow-up Question
      ↓
Continue Interview
```

Only after this works should voice capabilities be implemented.

---

# Final Instruction to OpenCode

First inspect the existing codebase and understand the current architecture.

Do not blindly create files.

Reuse existing:

- Models
- Authentication
- Company/tenant system
- Jobs
- Candidates
- Skills
- Routes
- Inertia layouts
- UI components
- Form patterns
- Testing conventions
- Factories and seeders

The existing UI is already implemented and should be treated as the visual contract.

The goal is **not** to create another dashboard.

The goal is to make the existing Interview AI interface work with a real, persistent, secure Interview domain.

Start with the database and domain model, then implement the interview lifecycle, then connect it to Inertia, and finally write the tests.

**Do not implement AI, voice, WebSockets, or real-time broadcasting in this milestone.**
