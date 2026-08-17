Candidate
    │
    ▼
Start Interview
    │
    ▼
Interviewing
    │
    ▼
Ask Question
    │
    ▼
Candidate Answers
    │
    ▼
Assess Answer
    │
    ├───────────────┐
    │               │
    ▼               ▼
Strong            Weak
    │               │
    │               ▼
    │         Generate Follow-up
    │               │
    └───────┬───────┘
            ▼
       Next Question
            │
            ▼
       More Questions?
        /          \
      Yes           No
       │             │
       └──────┐      ▼
              │   Complete
              │      │
              ▼      ▼
           Interview Report


app/
│
├── Identity/
│   ├── Authentication/
│   ├── Authorization/
│   └── Organizations/
│
├── Candidates/
│   ├── RegisterCandidate/
│   ├── UpdateCandidate/
│   ├── UploadResume/
│   └── ViewCandidate/
│
├── Positions/
│   ├── CreatePosition/
│   ├── UpdatePosition/
│   ├── DefineSkills/
│   └── ConfigureInterview/
│
├── Interviewing/
│   ├── ScheduleInterview/
│   ├── StartInterview/
│   ├── AskQuestion/
│   ├── SubmitAnswer/
│   ├── AdvanceInterview/
│   ├── PauseInterview/
│   ├── ResumeInterview/
│   └── CompleteInterview/
│
├── Assessments/
│   ├── EvaluateAnswer/
│   ├── ScoreSkill/
│   ├── CalculateScore/
│   └── AssessCandidate/
│
├── QuestionBank/
│   ├── CreateQuestion/
│   ├── GenerateQuestion/
│   ├── FindNextQuestion/
│   └── ManageQuestion/
│
├── Reporting/
│   ├── GenerateReport/
│   ├── CandidateSummary/
│   └── CompareCandidates/
│
├── Hiring/
│   ├── RecommendCandidate/
│   ├── ShortlistCandidate/
│   └── RejectCandidate/
│
├── AI/
│   ├── Interviewer/
│   ├── QuestionGenerator/
│   ├── AnswerEvaluator/
│   └── ReportGenerator/
│
└── Shared/
    ├── Bus/
    ├── ValueObjects/
    ├── Exceptions/
    └── Support/