Role

You are working on a Laravel application for an AI-powered technical interviewing platform.

Your task is to implement the first complete business capability:

ConductInterview — a candidate can start a technical interview, receive AI-generated questions, submit answers, have answers evaluated, receive adaptive follow-up questions, and complete the interview with a structured assessment.

The implementation must follow Vertical Slice Architecture and the existing project's business-oriented organization.

Do not introduce a traditional layered architecture such as:

app/
├── Controllers/
├── Services/
├── Repositories/
├── Jobs/
└── Models/

Instead, organize code around business capabilities/use cases.

1. Technology Stack

Use the following stack:

PHP 8.4+
Laravel 13
PostgreSQL
pgvector where useful, but do not introduce vector search unless required for this feature
Redis
Laravel Horizon
Laravel AI SDK
Filament 5 for internal/admin functionality
Inertia.js 3
Vue 3
TypeScript
Tailwind CSS 4
Pest for testing

Follow Laravel conventions wherever they do not conflict with the vertical-slice architecture.

2. Primary Goal

Implement the first usable version of the interview engine.

The complete flow should be:

Position
    ↓
Candidate
    ↓
Create Interview
    ↓
Start Interview
    ↓
Generate First Question
    ↓
Display Question
    ↓
Candidate Answers
    ↓
Evaluate Answer
    ↓
Update Skill Assessment
    ↓
Determine Next Question
    ↓
Display Next Question
    ↓
...
    ↓
Complete Interview
    ↓
Generate Final Assessment

The implementation should support an adaptive interview.

The AI should not simply generate 10 unrelated questions.

It should use:

Position requirements
Required skills
Interview configuration
Previous questions
Candidate answers
Previous evaluations
Current skill scores
Interview progress
Remaining questions/time

to determine what should be asked next.

3. Architecture

Use the following high-level structure:

app/
├── Candidates/
├── Positions/
├── Interviewing/
├── Assessments/
├── QuestionBank/
├── AI/
└── Shared/

For this task, focus primarily on:

app/Interviewing/
app/Assessments/
app/AI/
app/Positions/
app/Candidates/
4. Vertical Slice Structure

Use feature/use-case directories.

For example:

app/
└── Interviewing/
    ├── CreateInterview/
    ├── StartInterview/
    ├── AskQuestion/
    ├── SubmitAnswer/
    ├── EvaluateAnswer/
    ├── SelectNextQuestion/
    ├── CompleteInterview/
    ├── ViewInterview/
    │
    ├── Interview.php
    ├── InterviewStatus.php
    └── InterviewType.php

Do not create generic directories such as:

app/Services
app/Repositories
app/Handlers
app/DTOs
app/Jobs

for the entire application.

If a command needs a handler, keep it with that command:

SubmitAnswer/
├── SubmitAnswer.php
├── SubmitAnswerHandler.php
├── SubmitAnswerRequest.php
└── SubmitAnswerJob.php

The goal is:

Everything required to understand and modify a business operation should be located close to that operation.

5. Business Concepts

The first version should contain these concepts.

Organization

The company using the platform.

Position

A job opening being interviewed for.

Example:

Senior Laravel Developer
Candidate

The person being interviewed.

Interview

A specific interview session between a candidate and a position.

Skill

A competency required by a position.

Example:

PHP
Laravel
SQL
Testing
Architecture
APIs
Vue.js
Question

A technical interview question.

Answer

A candidate's answer to a question.

Evaluation

The AI's structured assessment of an answer.

Assessment

The accumulated assessment of the candidate during the interview.

6. Database Design

Use normal relational PostgreSQL tables.

Do not over-engineer the schema.

positions

Expected fields:

id
organization_id
title
description
level
duration_minutes
question_count
status
created_at
updated_at

Use an enum or suitable constrained values for:

level
status

Example levels:

junior
mid
senior
lead
7. Position Skills

Create a pivot table:

position_skills

Fields:

position_id
skill_id
weight
required
created_at
updated_at

Example:

Laravel       weight: 10   required: true
PHP           weight: 10   required: true
SQL           weight: 8    required: true
Testing       weight: 7    required: true
Architecture  weight: 9    required: true
Vue           weight: 5    required: false
8. skills

Fields:

id
name
slug
description
created_at
updated_at

Examples:

PHP
Laravel
SQL
PostgreSQL
MySQL
REST APIs
Testing
Architecture
Security
Vue.js
JavaScript
9. candidates

At minimum:

id
organization_id
name
email
created_at
updated_at

Do not implement CV parsing yet.

10. interviews

Fields:

id
organization_id
position_id
candidate_id
status
type
started_at
completed_at
current_question_id
question_index
total_questions
metadata
created_at
updated_at

Status:

scheduled
in_progress
paused
completed
cancelled

Type:

technical
behavioral
mixed

For this first feature, focus on:

technical
11. questions

Fields:

id
skill_id
type
difficulty
question
expected_topics
evaluation_guidance
is_active
created_at
updated_at

Question types could include:

conceptual
practical
debugging
architecture
scenario
code_review

Difficulty:

easy
medium
hard

expected_topics can be JSON.

Example:

[
    "dependency injection",
    "service container",
    "bindings",
    "interfaces"
]
12. Interview Questions

Create a table such as:

interview_questions

This represents a question actually asked during an interview.

Fields:

id
interview_id
question_id
position
skill_id
difficulty
question_text
status
asked_at
answered_at
created_at
updated_at

Why duplicate question_text?

Because the question presented to the candidate should be immutable.

The question bank may later change.

The interview needs to preserve exactly what was asked.

13. Answers

Create:

answers

Fields:

id
interview_question_id
candidate_id
content
submitted_at
created_at
updated_at

For now, content is text.

Do not implement voice/audio yet.

14. Evaluations

Create:

evaluations

Fields:

id
answer_id
score
technical_accuracy
depth
practical_experience
communication
confidence
strengths
weaknesses
missing_topics
follow_up_required
reasoning_summary
created_at
updated_at

Scores should use a consistent scale.

Use:

0-10

Do not expose raw LLM reasoning or chain-of-thought to users.

reasoning_summary must contain only a concise, user-safe explanation.

15. Skill Assessments

Create:

skill_assessments

Fields:

id
interview_id
skill_id
score
confidence
questions_answered
updated_at
created_at

The score should be updated as the interview progresses.

Example:

Laravel = 8.7
PHP = 8.1
SQL = 6.5
Testing = 5.8
Architecture = 8.4
16. Final Assessment

Create:

assessments

Fields:

id
interview_id
overall_score
recommendation
confidence
strengths
weaknesses
skill_summary
summary
created_at
updated_at

Recommendation values:

strong_hire
hire
mixed
no_hire
strong_no_hire
17. Eloquent Models

Create only models that are actually required.

Suggested models:

Position
Skill
Candidate
Interview
Question
InterviewQuestion
Answer
Evaluation
SkillAssessment
Assessment

Models should contain relationships and simple domain behavior.

Do not put the entire interview engine into the Interview model.

Avoid:

$interview->runAiInterview();

Prefer explicit use cases:

StartInterview
SubmitAnswer
EvaluateAnswer
SelectNextQuestion
CompleteInterview
18. Create Interview

Implement:

Interviewing/CreateInterview

The command should accept:

position
candidate
type

Validate:

Position belongs to the current organization
Candidate belongs to the current organization
Position is active
Candidate is eligible for an interview

Create an interview with:

status = scheduled

Do not start the interview automatically.

19. Start Interview

Implement:

Interviewing/StartInterview

When the interview starts:

Verify the interview is scheduled.
Change status to in_progress.
Set started_at.
Determine total questions.
Generate/select the first question.
Persist the interview question.
Update current_question_id.
Set question_index = 1.

The first question should be generated based on:

Position
Position skills
Required skills
Candidate level
Interview type
Question count
20. Interview Question Strategy

Do not allow the LLM to completely control the interview.

The application should control:

Maximum questions
Maximum duration
Allowed skills
Difficulty range
Interview status
Question history

The AI can recommend:

skill
difficulty
question type
follow-up

The application validates the recommendation.

21. AI Interviewer

Use the Laravel AI SDK.

Create an AI agent dedicated to interviewing.

Suggested location:

app/AI/Interviewer/

For example:

app/AI/Interviewer/
├── TechnicalInterviewer.php
├── InterviewContext.php
└── InterviewDecision.php

The agent should receive structured context.

Example:

Position:
Senior Laravel Developer


Level:
Senior


Required skills:
PHP
Laravel
SQL
Testing
Architecture


Interview progress:
Question 5 / 12


Previous questions:
...


Previous evaluations:
...


Current skill scores:
Laravel: 8.5
PHP: 8.0
SQL: 6.2
Testing: 5.0


Current focus:
SQL
22. AI Must Return Structured Output

Do not parse free-form AI responses.

The AI should return structured data.

Example:

{
    "skill": "sql",
    "difficulty": "medium",
    "type": "scenario",
    "question": "How would you diagnose a slow query...",
    "reason": "The candidate has demonstrated basic SQL knowledge but has not yet demonstrated query optimization skills."
}

Create a DTO/value object around this result.

For example:

InterviewDecision

with:

skill
difficulty
type
question
reason

Do not expose the reason directly to the candidate.

23. Answer Evaluation Agent

Create:

app/AI/AnswerEvaluator/

Example:

AnswerEvaluator.php
EvaluationResult.php

The evaluator receives:

Position
Skill
Question
Expected topics
Candidate answer
Candidate level

Return structured output:

{
    "score": 8,
    "technical_accuracy": 8,
    "depth": 7,
    "practical_experience": 9,
    "communication": 8,
    "confidence": 0.91,
    "strengths": [
        "Understands dependency injection",
        "Provides practical examples"
    ],
    "weaknesses": [
        "Does not explain contextual bindings"
    ],
    "missing_topics": [
        "contextual bindings"
    ],
    "follow_up_required": true,
    "summary": "Strong understanding of Laravel dependency injection with a minor gap around contextual bindings."
}

Never store or display hidden chain-of-thought.

24. Submit Answer

Implement:

Interviewing/SubmitAnswer

The command should:

Verify interview is in progress.
Verify the question belongs to the interview.
Verify the question is the current question.
Verify the question has not already been answered.
Validate answer content.
Save the answer.
Mark the interview question as answered.
Dispatch answer evaluation.
Return an appropriate response to the candidate.

The HTTP request must not contain AI logic.

25. Asynchronous Evaluation

Use a queued job for AI evaluation.

Example:

Interviewing/SubmitAnswer/
└── EvaluateSubmittedAnswerJob.php

or place the evaluation workflow inside:

Assessments/EvaluateAnswer/

Prefer the latter because evaluating an answer is an assessment capability.

Flow:

SubmitAnswer
    ↓
Save Answer
    ↓
Dispatch EvaluateAnswer
    ↓
Queue
    ↓
AnswerEvaluator
    ↓
Evaluation
    ↓
SkillAssessment
    ↓
SelectNextQuestion

Use Redis/Horizon.

26. Evaluation Workflow

Implement:

Assessments/EvaluateAnswer

The handler should:

Load the answer.
Load the interview question.
Load the question.
Load the position.
Load relevant skill information.
Call AnswerEvaluator.
Validate the structured result.
Persist the evaluation.
Update skill assessment.
Determine whether a follow-up is required.
Determine whether the interview should continue.

Do not let the AI directly write to the database.

27. Skill Score Calculation

Do not simply overwrite the skill score with the latest answer.

Create a deterministic application-level scoring strategy.

For example:

previous score × previous confidence
+
new score × new confidence

or another clearly documented weighted approach.

The exact algorithm should be encapsulated in something like:

Assessments/Scoring/

Do not put scoring mathematics into the AI prompt.

The AI evaluates the answer.

Your application calculates the candidate's actual score.

28. Selecting the Next Question

Implement:

Interviewing/SelectNextQuestion

The selection process should consider:

1. Required skills not sufficiently assessed
2. Current skill confidence
3. Previous answers
4. Candidate performance
5. Interview progress
6. Question count
7. Difficulty
8. Avoiding repeated questions

The application determines whether another question is needed.

The AI can recommend the next skill/difficulty.

29. Adaptive Difficulty

Implement basic adaptive difficulty.

Example:

Score >= 8
    → increase difficulty


Score 6-7.9
    → maintain difficulty


Score < 6
    → decrease difficulty or ask clarifying question

Keep this rule deterministic.

The AI can provide supporting context, but your business rules should control it.

30. Follow-Up Questions

If:

follow_up_required = true

allow the AI to generate a focused follow-up.

Example:

Original:
Explain Laravel queues.


Candidate:
Queues allow us to run tasks asynchronously.


Follow-up:
How would you handle failed jobs and retries in
a production Laravel application?

Follow-ups should:

Stay related to the same skill.
Not repeat the original question.
Target a detected knowledge gap.
Count toward the interview question limit.
31. Interview Completion

Implement:

Interviewing/CompleteInterview

Complete when:

question_count >= maximum_questions

or:

time >= duration

or the recruiter manually completes the interview.

When completing:

status = completed
completed_at = now()

Then dispatch:

GenerateFinalAssessment
32. Final Assessment

Create:

Assessments/AssessCandidate

The final assessment should aggregate:

Skill assessments
Answer evaluations
Position requirements
Interview performance

Produce:

Overall score
Recommendation
Confidence
Strengths
Weaknesses
Skill breakdown
Summary

Example:

Overall: 8.1


Recommendation:
Strong Hire


Skills:


Laravel        9.0
PHP            8.5
Architecture   8.4
SQL            7.1
Testing        6.3


Strengths:
- Strong Laravel architecture knowledge
- Good practical experience
- Strong debugging ability


Concerns:
- Testing depth could be improved
- Advanced SQL optimization is weaker
33. Candidate UI

Implement the candidate-facing interview UI with:

Inertia.js
Vue 3
TypeScript
Tailwind CSS 4

Create:

resources/js/Pages/Interviews/
├── Show.vue
├── Components/
│   ├── InterviewHeader.vue
│   ├── QuestionCard.vue
│   ├── AnswerInput.vue
│   ├── InterviewProgress.vue
│   └── InterviewComplete.vue

The interface should be intentionally simple.

34. Candidate Interview Screen

The candidate should see:

┌────────────────────────────────────────────┐
│ Interview AI                               │
│                                            │
│ Senior Laravel Developer                   │
│                                            │
│ Question 5 of 12                    42%     │
│                                            │
│ ────────────────────────────────────────── │
│                                            │
│ How would you design a Laravel application │
│ that needs to process 100,000 jobs per     │
│ hour?                                      │
│                                            │
│ ┌────────────────────────────────────────┐ │
│ │ Write your answer...                  │ │
│ │                                        │ │
│ │                                        │ │
│ └────────────────────────────────────────┘ │
│                                            │
│                       [ Submit Answer ]     │
└────────────────────────────────────────────┘

Do not expose:

AI evaluation
Candidate score
Skill score
AI reasoning
Internal interview state

until the interview is completed.

35. Loading States

After submitting an answer:

Analyzing your answer...

Do not show a fake progress percentage.

Use a proper loading state.

Once the next question is available:

Next question

For MVP, polling is acceptable.

Do not introduce WebSockets unless they are actually needed.

36. Recruiter Results Screen

After completion, create:

resources/js/Pages/Interviews/Results.vue

Display:

Candidate
Position
Interview date


Overall Score
Recommendation


Skill Breakdown


Strengths


Areas for Improvement


Interview Summary

Use visual components such as:

score cards
progress bars
skill cards
recommendation badge

Avoid overly complex charts for the first version.

37. Filament

Create basic Filament resources for:

Position
Candidate
Interview
Question
Skill

The recruiter should be able to:

Create Position
Assign Skills
Create Candidate
Create Interview
Start Interview
View Completed Interview
View Assessment

Do not build a full ATS yet.

38. API / HTTP Boundary

Keep controllers thin.

A controller should:

Validate request
    ↓
Create command
    ↓
Call handler
    ↓
Return response

It should NOT:

Call OpenAI
Calculate scores
Choose questions
Modify interview state

Those responsibilities belong to the business slices.

39. Authorization

Every organization-owned resource must be scoped to the current organization.

A user from:

Organization A

must never be able to access:

Organization B

through:

ID manipulation
API requests
Filament
Inertia routes

Implement policies/authorization as part of the relevant slice.

Do not rely solely on UI restrictions.

40. Transactions

Use database transactions around state-changing operations.

Especially:

StartInterview
SubmitAnswer
EvaluateAnswer
CompleteInterview

For example:

Submit Answer
    ├── Save answer
    ├── Mark question answered
    └── Dispatch evaluation

Make sure the database state is consistent before dispatching asynchronous work.

41. Idempotency

This is important.

AI requests and queues can be retried.

EvaluateAnswer must be idempotent.

If an answer already has an evaluation:

Do not create another evaluation.

Likewise:

SubmitAnswer

must not create duplicate answers if the request is retried.

Use appropriate database constraints.

42. Database Constraints

Add unique constraints where appropriate.

Examples:

one evaluation per answer
one answer per interview question

Avoid relying only on PHP-level validation.

43. Error Handling

Handle:

AI provider unavailable
AI timeout
Invalid AI response
Malformed structured output
Queue failure
Interview expired
Candidate submits twice
Interview already completed
Question already answered

The candidate should receive friendly messages.

Never expose:

OpenAI exception
stack trace
prompt
API key
internal AI response
44. AI Provider Abstraction

Do not couple the business logic to OpenAI.

Create an interface such as:

interface TechnicalInterviewer
{
    public function selectQuestion(
        InterviewContext $context
    ): InterviewDecision;
}

and:

interface AnswerEvaluator
{
    public function evaluate(
        EvaluationContext $context
    ): EvaluationResult;
}

The implementation can use Laravel AI SDK.

The business layer should depend on the abstraction.

45. AI Prompt Design

Prompts should explicitly define the role.

Example interviewer instructions:

You are a senior technical interviewer.


Your job is to assess a candidate's technical
knowledge for the specified position.


You must:


- Ask one question at a time.
- Focus only on required position skills.
- Adapt difficulty based on previous answers.
- Avoid repeating questions.
- Prefer practical questions for senior candidates.
- Ask follow-ups when there is an important knowledge gap.
- Do not make hiring decisions yourself.
- Return structured output only.

The AI should never be allowed to:

modify interview status
change database records
assign final recommendation
access unrelated candidates
access other organizations
46. Prompt Context

Never send unnecessary candidate information to the AI.

Only send what is needed:

Position title
Position description
Required skills
Candidate level
Interview type
Question history
Answer history
Skill assessments
Current progress

Do not send:

passwords
tokens
internal IDs unless necessary
other candidates
organization secrets
47. Testing Strategy

Use Pest.

Every business slice should have tests.

At minimum:

Create Interview
it creates an interview
it rejects inactive positions
it rejects candidates from another organization
Start Interview
it starts a scheduled interview
it cannot start a completed interview
it generates the first question
it stores the first interview question
Submit Answer
it stores an answer
it rejects answers for another interview
it rejects duplicate answers
it dispatches evaluation
Evaluate Answer
it evaluates an answer
it stores structured evaluation
it updates skill assessment
it is idempotent
Select Next Question
it selects a new skill
it avoids previously asked questions
it increases difficulty after strong answers
it decreases difficulty after weak answers
Complete Interview
it completes the interview
it cannot complete an already completed interview
it dispatches final assessment
48. AI Testing

Do not make normal tests depend on real AI API calls.

Use Laravel AI SDK fakes/mocks.

Example conceptual behavior:

Fake AI
    ↓
returns predefined evaluation
    ↓
application processes evaluation
    ↓
assert database state

Create deterministic test scenarios.

For example:

Scenario:
Candidate answers Laravel question strongly.


AI result:
score = 9


Expected:
Laravel skill score increases.
Next question difficulty increases.

And:

Scenario:
Candidate answers SQL question poorly.


AI result:
score = 4


Expected:
SQL remains a weak skill.
Follow-up question is generated.
49. Feature Tests

Write end-to-end feature tests for the complete happy path:

Create Position
    ↓
Create Candidate
    ↓
Create Interview
    ↓
Start Interview
    ↓
Submit Answer
    ↓
Evaluate Answer
    ↓
Next Question
    ↓
Complete Interview
    ↓
Final Assessment

The goal is to have one test proving that the entire business capability works.

50. Do Not Build Voice Yet

This milestone is deliberately text-only.

Do not implement:

microphone
audio recording
speech-to-text
text-to-speech
video
realtime voice

The architecture should make those possible later, but they are outside this slice.

Eventually:

Text Answer

can become:

Answer
├── Text
├── Audio
└── Transcript

But don't complicate the initial implementation.

51. Do Not Build CV Parsing Yet

Do not implement:

PDF parsing
CV embeddings
CV analysis
LinkedIn import

The candidate can simply have:

name
email

for this milestone.

52. Do Not Build Billing

Do not implement:

Stripe
subscriptions
plans
usage billing
credits

The goal is to validate the interview engine first.

53. Definition of Done

The feature is complete when the following works:

[ ] Recruiter creates a Position
[ ] Recruiter assigns required Skills
[ ] Recruiter creates a Candidate
[ ] Recruiter creates an Interview
[ ] Candidate starts the Interview
[ ] AI generates the first technical question
[ ] Candidate submits an answer
[ ] Answer is stored
[ ] Answer evaluation runs asynchronously
[ ] Evaluation is stored
[ ] Skill assessment is updated
[ ] Next question is selected
[ ] Question difficulty adapts
[ ] Follow-up questions can be generated
[ ] Candidate can continue the interview
[ ] Interview completes after configured limit
[ ] Final assessment is generated
[ ] Recruiter can view the assessment
[ ] Authorization is enforced
[ ] AI calls are testable/fakeable
[ ] Duplicate submissions are prevented
[ ] Queue retries do not create duplicate evaluations
[ ] Feature tests cover the complete flow
54. Important Architectural Rules

Follow these rules throughout implementation.

Rule 1 — Business first

Organize code around:

What the application does

not:

What technology it uses
Rule 2 — Commands represent actions

Good:

StartInterview
SubmitAnswer
CompleteInterview
EvaluateAnswer

Avoid generic commands like:

ProcessData
HandleInterview
RunAI
Rule 3 — Handlers own use-case orchestration

A handler can coordinate:

domain models
AI services
events
jobs
transactions

but should not become a 500-line god class.

Rule 4 — AI is infrastructure/intelligence

AI should provide:

question recommendations
evaluation
summaries

The application owns:

state
authorization
business rules
scoring rules
limits
persistence
Rule 5 — Never trust the AI

Treat AI output as untrusted input.

Validate:

skill
difficulty
score
question type
recommendation

before using it.

Rule 6 — Keep AI structured

Never build business logic around parsing:

"Sure! Here's a great question..."

Use structured output.

Rule 7 — Keep slices independent

Avoid unnecessary dependencies between slices.

For example:

Assessments

should not directly reach deep inside:

Interviewing/SubmitAnswer

Instead use explicit commands, events, or shared domain contracts.

55. Expected First Milestone

At the end of this implementation, the application should feel like a very small but functional product:

Recruiter
    │
    ├── Create Position
    │
    ├── Add Skills
    │
    ├── Add Candidate
    │
    └── Start Interview
             │
             ▼
       ┌───────────────┐
       │  AI Interview │
       └───────┬───────┘
               │
       Question → Answer
               │
               ▼
        AI Evaluation
               │
               ▼
       Adaptive Question
               │
              ...
               │
               ▼
       Final Assessment

Prioritize correctness, architecture, testability, and a working end-to-end flow over visual polish.

Do not prematurely optimize or introduce infrastructure that isn't required for this milestone.

The final implementation should be ready to serve as the foundation for the next vertical slices: candidate management, richer position configuration, voice interviews, reporting, and hiring recommendations.