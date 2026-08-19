<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Assessment;
use App\Models\Candidate;
use App\Models\Evaluation;
use App\Models\Interview;
use App\Models\InterviewQuestion;
use App\Models\Organization;
use App\Models\Position;
use App\Models\Question;
use App\Models\Skill;
use App\Models\SkillAssessment;
use App\Models\User;
use App\Shared\Enums\AssessmentRecommendation;
use App\Shared\Enums\InterviewStatus;
use App\Shared\Enums\InterviewType;
use App\Shared\Enums\PositionLevel;
use App\Shared\Enums\QuestionDifficulty;
use App\Shared\Enums\QuestionStatus;
use App\Shared\Enums\QuestionType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as SupportCollection;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::factory()->create([
            'name' => 'Acme Corp',
            'slug' => 'acme-corp',
        ]);

        User::factory()->create([
            'name' => 'Alex Morgan',
            'email' => 'alex@acme.com',
            'organization_id' => $organization->id,
        ]);

        $this->createSkills();
        $this->createPositions($organization);
        $this->createCandidates($organization);
        $this->createQuestions();

        $skills = Skill::all();
        $positions = Position::all();
        $candidates = Candidate::all();
        $questions = Question::all();

        $this->createCompletedInterviews($organization, $positions, $candidates, $skills, $questions);
        $this->createInProgressInterview($organization, $positions, $candidates, $skills, $questions);
        $this->createScheduledInterviews($organization, $positions, $candidates);
    }

    private function createSkills(): void
    {
        $skillsData = [
            ['name' => 'PHP', 'slug' => 'php', 'description' => 'PHP programming language fundamentals and advanced concepts'],
            ['name' => 'Laravel', 'slug' => 'laravel', 'description' => 'Laravel framework ecosystem, patterns, and best practices'],
            ['name' => 'SQL', 'slug' => 'sql', 'description' => 'SQL queries, optimization, and database design'],
            ['name' => 'Testing', 'slug' => 'testing', 'description' => 'Unit testing, feature testing, and TDD practices'],
            ['name' => 'Architecture', 'slug' => 'architecture', 'description' => 'Application architecture, design patterns, and system design'],
            ['name' => 'Vue.js', 'slug' => 'vuejs', 'description' => 'Vue.js frontend framework and ecosystem'],
            ['name' => 'REST APIs', 'slug' => 'rest-apis', 'description' => 'RESTful API design and implementation'],
            ['name' => 'Security', 'slug' => 'security', 'description' => 'Web application security best practices'],
        ];

        foreach ($skillsData as $data) {
            Skill::create($data);
        }
    }

    private function createPositions(Organization $organization): void
    {
        $skills = Skill::all();

        $positionsData = [
            [
                'title' => 'Senior Laravel Developer',
                'description' => 'We are looking for a senior Laravel developer to lead our backend team. You will be responsible for building scalable APIs, mentoring junior developers, and making architectural decisions.',
                'level' => PositionLevel::Senior,
                'duration_minutes' => 60,
                'question_count' => 12,
                'required_skills' => ['php', 'laravel', 'sql', 'testing', 'architecture'],
                'optional_skills' => ['vuejs'],
            ],
            [
                'title' => 'Mid-Level PHP Developer',
                'description' => 'Join our team as a mid-level PHP developer. You will work on maintaining and extending our existing Laravel applications.',
                'level' => PositionLevel::Mid,
                'duration_minutes' => 45,
                'question_count' => 10,
                'required_skills' => ['php', 'laravel', 'sql'],
                'optional_skills' => ['testing', 'rest-apis'],
            ],
            [
                'title' => 'Full Stack Developer',
                'description' => 'A full stack role requiring both backend Laravel expertise and frontend Vue.js skills.',
                'level' => PositionLevel::Mid,
                'duration_minutes' => 60,
                'question_count' => 12,
                'required_skills' => ['php', 'laravel', 'vuejs', 'rest-apis'],
                'optional_skills' => ['sql', 'testing'],
            ],
        ];

        foreach ($positionsData as $data) {
            $requiredSkills = $data['required_skills'];
            $optionalSkills = $data['optional_skills'];
            unset($data['required_skills'], $data['optional_skills']);

            $position = Position::create([
                ...$data,
                'organization_id' => $organization->id,
                'status' => 'active',
            ]);

            foreach ($requiredSkills as $slug) {
                $skill = $skills->firstWhere('slug', $slug);
                if ($skill) {
                    $position->skills()->attach($skill->id, [
                        'weight' => $slug === 'laravel' ? 10 : ($slug === 'php' ? 9 : 7),
                        'required' => true,
                    ]);
                }
            }

            foreach ($optionalSkills as $slug) {
                $skill = $skills->firstWhere('slug', $slug);
                if ($skill) {
                    $position->skills()->attach($skill->id, [
                        'weight' => 5,
                        'required' => false,
                    ]);
                }
            }
        }
    }

    private function createCandidates(Organization $organization): void
    {
        $candidatesData = [
            ['name' => 'John Smith', 'email' => 'john.smith@email.com'],
            ['name' => 'Sarah Johnson', 'email' => 'sarah.johnson@email.com'],
            ['name' => 'Michael Chen', 'email' => 'michael.chen@email.com'],
            ['name' => 'Emily Rodriguez', 'email' => 'emily.rodriguez@email.com'],
            ['name' => 'David Kim', 'email' => 'david.kim@email.com'],
            ['name' => 'Jessica Patel', 'email' => 'jessica.patel@email.com'],
            ['name' => 'James Wilson', 'email' => 'james.wilson@email.com'],
            ['name' => 'Lisa Thompson', 'email' => 'lisa.thompson@email.com'],
        ];

        foreach ($candidatesData as $data) {
            Candidate::create([
                ...$data,
                'organization_id' => $organization->id,
            ]);
        }
    }

    private function createQuestions(): void
    {
        $questionsData = [
            'php' => [
                ['type' => QuestionType::Conceptual, 'difficulty' => QuestionDifficulty::Medium, 'question' => 'Explain the differences between abstract classes and interfaces in PHP. When would you use each?', 'expected_topics' => ['abstract classes', 'interfaces', 'polymorphism', 'contracts']],
                ['type' => QuestionType::Practical, 'difficulty' => QuestionDifficulty::Hard, 'question' => 'How would you implement a repository pattern in PHP to abstract database access? Walk me through the design decisions.', 'expected_topics' => ['repository pattern', 'abstraction', 'dependency injection', 'SOLID']],
                ['type' => QuestionType::Scenario, 'difficulty' => QuestionDifficulty::Medium, 'question' => 'You need to process a large CSV file with 1 million rows. How would you approach this in PHP without running out of memory?', 'expected_topics' => ['memory management', 'generators', 'chunking', 'streaming']],
            ],
            'laravel' => [
                ['type' => QuestionType::Conceptual, 'difficulty' => QuestionDifficulty::Medium, 'question' => "Explain how Laravel's service container works. What are service providers and why are they important?", 'expected_topics' => ['service container', 'service providers', 'dependency injection', 'bindings']],
                ['type' => QuestionType::Practical, 'difficulty' => QuestionDifficulty::Hard, 'question' => 'Design a job queue system for sending 100,000 emails. How would you handle failures, retries, and rate limiting?', 'expected_topics' => ['queues', 'jobs', 'retry logic', 'rate limiting', 'failed jobs']],
                ['type' => QuestionType::Architecture, 'difficulty' => QuestionDifficulty::Hard, 'question' => 'How would you structure a Laravel application that needs to handle multiple tenants? Explain your approach to data isolation.', 'expected_topics' => ['multi-tenancy', 'data isolation', 'middleware', 'database design']],
                ['type' => QuestionType::Debugging, 'difficulty' => QuestionDifficulty::Medium, 'question' => 'Your Laravel application is experiencing N+1 query issues. How would you identify and fix these problems?', 'expected_topics' => ['N+1 queries', 'eager loading', 'query optimization', 'debugging']],
            ],
            'sql' => [
                ['type' => QuestionType::Practical, 'difficulty' => QuestionDifficulty::Medium, 'question' => 'Write a SQL query to find the top 5 customers by order volume in the last 30 days. Include their total spent and order count.', 'expected_topics' => ['aggregation', 'JOIN', 'WHERE', 'GROUP BY', 'ORDER BY']],
                ['type' => QuestionType::Architecture, 'difficulty' => QuestionDifficulty::Hard, 'question' => 'How would you optimize a slow query that joins 5 tables and returns millions of rows? Walk me through your approach.', 'expected_topics' => ['query optimization', 'indexing', 'EXPLAIN', 'partitioning']],
            ],
            'testing' => [
                ['type' => QuestionType::Conceptual, 'difficulty' => QuestionDifficulty::Medium, 'question' => 'What is the difference between unit tests, feature tests, and integration tests? When would you use each?', 'expected_topics' => ['testing pyramid', 'unit tests', 'feature tests', 'mocking']],
                ['type' => QuestionType::Practical, 'difficulty' => QuestionDifficulty::Medium, 'question' => 'How would you test a Laravel job that sends an email and updates a database record? Show me your approach.', 'expected_topics' => ['testing jobs', 'queue fake', 'mail fake', 'database assertions']],
            ],
            'architecture' => [
                ['type' => QuestionType::Architecture, 'difficulty' => QuestionDifficulty::Hard, 'question' => 'Design a system for real-time notifications that needs to support web, mobile, and email channels. How would you ensure reliability and scalability?', 'expected_topics' => ['system design', 'message queues', 'websockets', 'scalability', 'reliability']],
                ['type' => QuestionType::Scenario, 'difficulty' => QuestionDifficulty::Medium, 'question' => 'Your monolithic Laravel application is becoming difficult to maintain. How would you approach decomposing it into services?', 'expected_topics' => ['monolith to microservices', 'service boundaries', 'migration strategy', 'DDD']],
            ],
            'vuejs' => [
                ['type' => QuestionType::Conceptual, 'difficulty' => QuestionDifficulty::Medium, 'question' => 'Explain the Vue 3 Composition API. How does it improve upon the Options API for complex components?', 'expected_topics' => ['Composition API', 'reactivity', 'composables', 'setup function']],
                ['type' => QuestionType::Practical, 'difficulty' => QuestionDifficulty::Hard, 'question' => 'How would you implement optimistic updates in a Vue 3 application with Inertia.js? Consider error handling and rollback.', 'expected_topics' => ['optimistic updates', 'Inertia.js', 'UX patterns', 'error handling']],
            ],
            'rest-apis' => [
                ['type' => QuestionType::Architecture, 'difficulty' => QuestionDifficulty::Medium, 'question' => 'How do you design a RESTful API for a complex domain with nested resources? Explain your approach to versioning and pagination.', 'expected_topics' => ['REST design', 'resource modeling', 'versioning', 'pagination', 'HATEOAS']],
            ],
            'security' => [
                ['type' => QuestionType::Scenario, 'difficulty' => QuestionDifficulty::Hard, 'question' => 'You discover a SQL injection vulnerability in production. Walk me through your incident response process and how you would prevent it in the future.', 'expected_topics' => ['SQL injection', 'incident response', 'input validation', 'prepared statements']],
            ],
        ];

        foreach ($questionsData as $slug => $skillQuestions) {
            $skill = Skill::where('slug', $slug)->first();
            if (! $skill) {
                continue;
            }

            foreach ($skillQuestions as $q) {
                Question::create([
                    'skill_id' => $skill->id,
                    'type' => $q['type'],
                    'difficulty' => $q['difficulty'],
                    'question' => $q['question'],
                    'expected_topics' => $q['expected_topics'],
                    'evaluation_guidance' => null,
                    'is_active' => true,
                ]);
            }
        }
    }

    /**
     * @param Collection<int, Skill> $skills
     * @param Collection<int, Question> $questions
     */
    private function createCompletedInterviews(
        Organization $organization,
        Collection $positions,
        Collection $candidates,
        Collection $skills,
        Collection $questions,
    ): void {
        /** @var Position $seniorPosition */
        $seniorPosition = $positions->firstWhere('title', 'Senior Laravel Developer');
        /** @var Position $midPosition */
        $midPosition = $positions->firstWhere('title', 'Mid-Level PHP Developer');

        $this->createCompletedInterview(
            $organization,
            $seniorPosition,
            /** @var Candidate */
            $candidates->firstWhere('name', 'John Smith'),
            $questions,
            8.4,
            AssessmentRecommendation::StrongHire,
            0.92,
        );

        $this->createCompletedInterview(
            $organization,
            $seniorPosition,
            /** @var Candidate */
            $candidates->firstWhere('name', 'Sarah Johnson'),
            $questions,
            7.2,
            AssessmentRecommendation::Hire,
            0.85,
        );

        $this->createCompletedInterview(
            $organization,
            $midPosition,
            /** @var Candidate */
            $candidates->firstWhere('name', 'Michael Chen'),
            $questions,
            5.8,
            AssessmentRecommendation::Mixed,
            0.72,
        );

        $this->createCompletedInterview(
            $organization,
            $seniorPosition,
            /** @var Candidate */
            $candidates->firstWhere('name', 'Emily Rodriguez'),
            $questions,
            4.2,
            AssessmentRecommendation::NoHire,
            0.78,
        );
    }

    /**
     * @param Collection<int, Question> $questions
     */
    private function createCompletedInterview(
        Organization $organization,
        Position $position,
        Candidate $candidate,
        Collection $questions,
        float $overallScore,
        AssessmentRecommendation $recommendation,
        float $confidence,
    ): void {
        $startedAt = Carbon::now()->subDays(rand(1, 14))->subHours(rand(1, 5));
        $completedAt = (clone $startedAt)->addMinutes(rand(30, 60));

        $interview = Interview::create([
            'organization_id' => $organization->id,
            'position_id' => $position->id,
            'candidate_id' => $candidate->id,
            'status' => InterviewStatus::Completed,
            'type' => InterviewType::Technical,
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
            'total_questions' => $position->question_count,
            'question_index' => $position->question_count,
        ]);

        $positionSkills = $position->skills()->get();
        $askedQuestions = $questions->random(min(8, $questions->count()));

        $questionIndex = 0;
        foreach ($askedQuestions as $question) {
            $questionIndex++;
            /** @var Skill $skill */
            $skill = $question->skill;

            $interviewQuestion = InterviewQuestion::create([
                'interview_id' => $interview->id,
                'question_id' => $question->id,
                'position' => $questionIndex,
                'skill_id' => $skill->id,
                'difficulty' => $question->difficulty,
                'question_text' => $question->question,
                'status' => QuestionStatus::Answered,
                'asked_at' => (clone $startedAt)->addMinutes($questionIndex * 4),
                'answered_at' => (clone $startedAt)->addMinutes($questionIndex * 4 + rand(2, 5)),
            ]);

            $score = $this->generateScoreForOverall($overallScore, $positionSkills->count());
            $answer = Answer::create([
                'interview_question_id' => $interviewQuestion->id,
                'candidate_id' => $candidate->id,
                'content' => $this->generateAnswerText($question, $skill, $score),
                'submitted_at' => $interviewQuestion->answered_at,
            ]);

            Evaluation::create([
                'answer_id' => $answer->id,
                'score' => $score,
                'technical_accuracy' => min(10, $score + rand(-1, 1)),
                'depth' => min(10, $score + rand(-2, 1)),
                'practical_experience' => min(10, $score + rand(-1, 2)),
                'communication' => min(10, $score + rand(0, 2)),
                'confidence' => rand(60, 95) / 100,
                'strengths' => $score >= 7 ? [$this->generateStrength($skill)] : null,
                'weaknesses' => $score < 7 ? [$this->generateWeakness($skill)] : null,
                'missing_topics' => $score < 6 ? $question->expected_topics : null,
                'follow_up_required' => $score < 6,
                'reasoning_summary' => $this->generateSummary($score, $skill),
            ]);
        }

        $skillScores = $this->calculateSkillScores($interview, $positionSkills);

        foreach ($skillScores as $skillId => $data) {
            SkillAssessment::create([
                'interview_id' => $interview->id,
                'skill_id' => $skillId,
                'score' => $data['score'],
                'confidence' => $data['confidence'],
                'questions_answered' => $data['count'],
            ]);
        }

        Assessment::create([
            'interview_id' => $interview->id,
            'overall_score' => $overallScore,
            'recommendation' => $recommendation,
            'confidence' => $confidence,
            'strengths' => $overallScore >= 7 ? [
                'Strong understanding of core concepts',
                'Good practical experience',
                'Clear communication style',
            ] : null,
            'weaknesses' => $overallScore < 7 ? [
                'Some gaps in advanced topics',
                'Could improve depth of answers',
            ] : null,
            'skill_summary' => $skillScores->map(fn ($data) => [
                'score' => $data['score'],
                'questions' => $data['count'],
            ])->toArray(),
            'summary' => $this->generateAssessmentSummary($overallScore, $recommendation),
        ]);
    }

    /**
     * @param Collection<int, Skill> $skills
     * @param Collection<int, Question> $questions
     */
    private function createInProgressInterview(
        Organization $organization,
        Collection $positions,
        Collection $candidates,
        Collection $skills,
        Collection $questions,
    ): void {
        /** @var Position $position */
        $position = $positions->firstWhere('title', 'Full Stack Developer');
        /** @var Candidate $candidate */
        $candidate = $candidates->firstWhere('name', 'David Kim');

        $startedAt = Carbon::now()->subMinutes(15);

        $interview = Interview::create([
            'organization_id' => $organization->id,
            'position_id' => $position->id,
            'candidate_id' => $candidate->id,
            'status' => InterviewStatus::InProgress,
            'type' => InterviewType::Technical,
            'started_at' => $startedAt,
            'total_questions' => $position->question_count,
            'question_index' => 3,
        ]);

        $askedQuestions = $questions->random(3);
        $currentQuestion = null;

        foreach ($askedQuestions as $index => $question) {
            $isAnswered = $index < 2;

            $interviewQuestion = InterviewQuestion::create([
                'interview_id' => $interview->id,
                'question_id' => $question->id,
                'position' => $index + 1,
                'skill_id' => $question->skill_id,
                'difficulty' => $question->difficulty,
                'question_text' => $question->question,
                'status' => $isAnswered ? QuestionStatus::Answered : QuestionStatus::Asked,
                'asked_at' => (clone $startedAt)->addMinutes($index * 5),
                'answered_at' => $isAnswered ? (clone $startedAt)->addMinutes($index * 5 + 3) : null,
            ]);

            if ($isAnswered) {
                $answer = Answer::create([
                    'interview_question_id' => $interviewQuestion->id,
                    'candidate_id' => $candidate->id,
                    'content' => 'I would approach this by first analyzing the requirements and then implementing a solution using Laravel best practices. For the frontend, I would use Vue 3 with Composition API...',
                    'submitted_at' => $interviewQuestion->answered_at,
                ]);

                $score = rand(60, 90) / 10;
                Evaluation::create([
                    'answer_id' => $answer->id,
                    'score' => $score,
                    'technical_accuracy' => min(10, $score + rand(-1, 1)),
                    'depth' => min(10, $score + rand(-1, 1)),
                    'practical_experience' => min(10, $score + rand(0, 2)),
                    'communication' => min(10, $score + rand(0, 2)),
                    'confidence' => rand(65, 90) / 100,
                    'strengths' => $score >= 7 ? ['Good understanding of the topic'] : null,
                    'weaknesses' => $score < 7 ? ['Could provide more depth'] : null,
                    'follow_up_required' => false,
                    'reasoning_summary' => 'Solid answer demonstrating practical knowledge.',
                ]);
            } else {
                $currentQuestion = $interviewQuestion;
            }
        }

        if ($currentQuestion) {
            $interview->update(['current_question_id' => $currentQuestion->id]);
        }
    }

    /**
     * @param Collection<int, Skill> $skills
     */
    private function createScheduledInterviews(
        Organization $organization,
        Collection $positions,
        Collection $candidates,
    ): void {
        /** @var Position $position */
        $position = $positions->firstWhere('title', 'Senior Laravel Developer');
        /** @var Candidate $candidate */
        $candidate = $candidates->firstWhere('name', 'Jessica Patel');

        Interview::create([
            'organization_id' => $organization->id,
            'position_id' => $position->id,
            'candidate_id' => $candidate->id,
            'status' => InterviewStatus::Scheduled,
            'type' => InterviewType::Technical,
            'total_questions' => $position->question_count,
            'question_index' => 0,
        ]);

        /** @var Position $position2 */
        $position2 = $positions->firstWhere('title', 'Mid-Level PHP Developer');
        /** @var Candidate $candidate2 */
        $candidate2 = $candidates->firstWhere('name', 'James Wilson');

        Interview::create([
            'organization_id' => $organization->id,
            'position_id' => $position2->id,
            'candidate_id' => $candidate2->id,
            'status' => InterviewStatus::Scheduled,
            'type' => InterviewType::Technical,
            'total_questions' => $position2->question_count,
            'question_index' => 0,
        ]);

        /** @var Position $position3 */
        $position3 = $positions->firstWhere('title', 'Full Stack Developer');
        /** @var Candidate $candidate3 */
        $candidate3 = $candidates->firstWhere('name', 'Lisa Thompson');

        Interview::create([
            'organization_id' => $organization->id,
            'position_id' => $position3->id,
            'candidate_id' => $candidate3->id,
            'status' => InterviewStatus::Scheduled,
            'type' => InterviewType::Technical,
            'total_questions' => $position3->question_count,
            'question_index' => 0,
        ]);
    }

    private function generateScoreForOverall(float $overallScore, int $skillCount): float
    {
        $variance = 2.0;
        $min = max(1, $overallScore - $variance);
        $max = min(10, $overallScore + $variance);

        return round(rand((int) ($min * 10), (int) ($max * 10)) / 10, 1);
    }

    private function generateAnswerText(Question $question, Skill $skill, float $score): string
    {
        if ($score >= 8) {
            return "Based on my experience, I would approach this by first understanding the core requirements. For {$skill->name}, I've found that the key is to balance practicality with best practices. In my previous role, I implemented a similar solution that handled high traffic efficiently. The main considerations are scalability, maintainability, and team familiarity with the approach.";
        }

        if ($score >= 6) {
            return "I would approach this systematically. First, I'd analyze the requirements and identify potential challenges. For this type of problem in {$skill->name}, I typically start with a basic implementation and then optimize based on specific needs. I've used this approach in several projects with good results.";
        }

        return "This is an interesting question about {$skill->name}. I would start by considering the basic requirements and then think about how to implement a solution. I have some experience with this area and would apply standard practices to solve the problem.";
    }

    private function generateStrength(Skill $skill): string
    {
        $strengths = [
            "Strong {$skill->name} knowledge",
            "Excellent understanding of {$skill->name} concepts",
            "Good practical {$skill->name} experience",
            "Solid {$skill->name} fundamentals",
            "Clear communication of {$skill->name} concepts",
        ];

        return $strengths[array_rand($strengths)];
    }

    private function generateWeakness(Skill $skill): string
    {
        $weaknesses = [
            "Could improve depth in {$skill->name}",
            "Some gaps in advanced {$skill->name} topics",
            "Limited experience with complex {$skill->name} scenarios",
            "Would benefit from more {$skill->name} practice",
        ];

        return $weaknesses[array_rand($weaknesses)];
    }

    private function generateSummary(float $score, Skill $skill): string
    {
        if ($score >= 8) {
            return "Strong {$skill->name} knowledge demonstrated with practical examples.";
        }

        if ($score >= 6) {
            return "Solid {$skill->name} understanding with room for deeper exploration.";
        }

        return "Basic {$skill->name} knowledge shown; additional study recommended.";
    }

    /**
     * @param Collection<int, Skill> $positionSkills
     * @return SupportCollection<int, array{score: float, confidence: float, count: int}>
     */
    private function calculateSkillScores(Interview $interview, Collection $positionSkills): SupportCollection
    {
        $answers = Answer::whereHas('interviewQuestion', fn ($q) => $q->where('interview_id', $interview->id))
            ->with(['evaluation', 'interviewQuestion.skill'])
            ->get();

        $scores = collect();
        foreach ($positionSkills as $skill) {
            /** @var int $skillId */
            $skillId = $skill->id;
            $skillAnswers = $answers->filter(fn ($a) => $a->interviewQuestion->skill_id === $skillId);

            if ($skillAnswers->isEmpty()) {
                $scores->put($skillId, [
                    'score' => 0,
                    'confidence' => 0,
                    'count' => 0,
                ]);

                continue;
            }

            $totalScore = 0;
            $totalConfidence = 0;
            $count = 0;

            foreach ($skillAnswers as $answer) {
                if ($answer->evaluation) {
                    /** @var float $score */
                    $score = $answer->evaluation->score;
                    /** @var float $confidenceVal */
                    $confidenceVal = $answer->evaluation->confidence;
                    $totalScore += $score;
                    $totalConfidence += $confidenceVal;
                    $count++;
                }
            }

            $scores->put($skillId, [
                'score' => $count > 0 ? round($totalScore / $count, 1) : 0,
                'confidence' => $count > 0 ? round($totalConfidence / $count, 2) : 0,
                'count' => $count,
            ]);
        }

        return $scores;
    }

    private function generateAssessmentSummary(float $overallScore, AssessmentRecommendation $recommendation): string
    {
        return match ($recommendation) {
            AssessmentRecommendation::StrongHire => 'The candidate demonstrated strong technical skills across multiple areas. They showed excellent practical experience and clear communication. Their answers were well-structured and showed depth of understanding. This candidate would be a valuable addition to the team.',
            AssessmentRecommendation::Hire => 'The candidate showed solid technical competence and good problem-solving skills. While there are some areas for improvement, they demonstrated sufficient knowledge and experience to perform well in the role. Recommended for hire.',
            AssessmentRecommendation::Mixed => 'The candidate showed competence in some areas but had gaps in others. Their answers were generally acceptable but lacked depth in certain topics. Consider a follow-up interview focusing on the weaker areas before making a final decision.',
            AssessmentRecommendation::NoHire => 'The candidate did not demonstrate sufficient technical skills for this role. There were significant gaps in fundamental concepts and practical experience. While they showed some potential, the gaps are too wide for the expected level.',
            AssessmentRecommendation::StrongNoHire => 'The candidate showed minimal technical competence across the assessed areas. Their answers indicated limited practical experience and gaps in fundamental concepts. Not recommended for this role.',
        };
    }
}
