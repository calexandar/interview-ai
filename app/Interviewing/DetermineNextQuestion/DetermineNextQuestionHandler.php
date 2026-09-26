<?php

namespace App\Interviewing\DetermineNextQuestion;

use App\Models\Evaluation;
use App\Models\Interview;
use App\Models\InterviewQuestion;
use App\Models\Skill;
use App\Shared\Enums\EvaluationStatus;
use App\Shared\Enums\PositionLevel;
use App\Shared\Enums\QuestionDifficulty;
use App\Shared\Enums\QuestionStatus;
use Illuminate\Support\Collection;

/**
 * Decides what the next question should target.
 *
 * This is deliberately deterministic and model-free: the same interview state
 * always produces the same plan, so the interview's shape is reproducible and
 * testable. The AI is asked to write a question for the plan, never to decide
 * the plan.
 */
class DetermineNextQuestionHandler
{
    /**
     * A score at or above this suggests the candidate is ready for more depth.
     */
    private const ADVANCE_THRESHOLD = 8.0;

    /**
     * A score at or below this suggests the candidate needs a gentler question.
     */
    private const REINFORCE_THRESHOLD = 5.0;

    private const MAX_FOCUS_CONCEPTS = 3;

    public function handle(DetermineNextQuestion $command): NextQuestionPlan
    {
        $interview = Interview::where('id', $command->interviewId)
            ->where('organization_id', $command->organizationId)
            ->firstOrFail();

        $position = $interview->position;
        $skills = $position->skills()->get();

        if ($skills->isEmpty()) {
            abort(422, 'This position has no skills configured.');
        }

        $questions = $interview->interviewQuestions()
            ->with(['answer.evaluation', 'skill'])
            ->get();

        $askedCounts = $this->askedCounts($questions);
        $skill = $this->targetSkill($skills, $askedCounts, (int) $position->question_count);

        $skillQuestions = $questions
            ->where('skill_id', $skill->id)
            ->whereIn('status', [
                QuestionStatus::Asking,
                QuestionStatus::Answering,
                QuestionStatus::Processing,
                QuestionStatus::Answered,
            ])
            ->sortByDesc('id')
            ->values();

        $lastEvaluation = $this->lastSettledEvaluation($skillQuestions);
        $missingConcepts = $this->focusConcepts($lastEvaluation);

        return new NextQuestionPlan(
            skillId: $skill->id,
            skillName: $skill->name,
            difficulty: $this->difficulty($skillQuestions, $lastEvaluation, $position->level),
            isFollowUp: $lastEvaluation !== null && ($missingConcepts !== [] || $lastEvaluation->follow_up_required),
            focusConcepts: $missingConcepts,
        );
    }

    /**
     * Picks the required skill furthest behind its weighted question quota, so
     * coverage stays proportional to how important the skill is for the role.
     * Ties break on weight then id, which keeps the choice reproducible.
     *
     * @param  Collection<int, Skill>  $skills
     * @param  array<int, int>  $askedCounts
     */
    private function targetSkill(Collection $skills, array $askedCounts, int $questionCount): Skill
    {
        $totalWeight = max(1, (int) $skills->sum(fn (Skill $skill): int => $this->weight($skill)));

        $ranked = $skills
            ->map(fn (Skill $skill): array => [
                'skill' => $skill,
                'deficit' => $this->quota($skill, $totalWeight, $questionCount) - ($askedCounts[$skill->id] ?? 0),
                'weight' => $this->weight($skill),
                'id' => $skill->id,
            ])
            ->sortBy([
                ['deficit', 'desc'],
                ['weight', 'desc'],
                ['id', 'asc'],
            ])
            ->values();

        return $ranked[0]['skill'];
    }

    /**
     * Skills are only ever weighted in the context of a position, so the
     * pivot carries the meaning. A skill fetched without one is unweighted.
     */
    private function weight(Skill $skill): int
    {
        $pivot = $skill->pivot;

        return $pivot === null ? 0 : (int) $pivot->getAttribute('weight');
    }

    private function quota(Skill $skill, int $totalWeight, int $questionCount): int
    {
        $quota = (int) round($questionCount * ($this->weight($skill) / $totalWeight));

        return max(1, $quota);
    }

    /**
     * @param  Collection<int, InterviewQuestion>  $questions
     * @return array<int, int>
     */
    private function askedCounts(Collection $questions): array
    {
        $asked = [QuestionStatus::Asking, QuestionStatus::Answering, QuestionStatus::Processing, QuestionStatus::Answered];

        $counts = [];

        foreach ($questions as $question) {
            if (! in_array($question->status, $asked, true)) {
                continue;
            }

            $skillId = (int) $question->skill_id;

            $counts[$skillId] = (int) (($counts[$skillId] ?? 0) + 1);
        }

        return $counts;
    }

    /**
     * @param  Collection<int, InterviewQuestion>  $skillQuestions
     */
    private function lastSettledEvaluation(Collection $skillQuestions): ?Evaluation
    {
        return $skillQuestions
            ->map(fn ($question): ?Evaluation => $question->answer?->evaluation)
            ->filter(fn (?Evaluation $evaluation): bool => $evaluation?->status === EvaluationStatus::Completed)
            ->first();
    }

    /**
     * Difficulty drifts by at most one step per question, based on how the
     * candidate actually performed on the most recent question for this skill.
     * A brand new skill starts from the seniority the role was advertised at.
     *
     * @param  Collection<int, InterviewQuestion>  $skillQuestions
     */
    private function difficulty(Collection $skillQuestions, ?Evaluation $lastEvaluation, PositionLevel $level): QuestionDifficulty
    {
        if ($skillQuestions->isEmpty()) {
            return $this->baselineDifficulty($level);
        }

        // Seeded from the floor of the scale so the walk only ever raises the
        // bar; the empty case is already handled above.
        $hardest = QuestionDifficulty::Easy;

        foreach ($skillQuestions as $question) {
            $hardest = $this->harder($hardest, $question->difficulty);
        }

        $difficulty = $hardest;

        if ($lastEvaluation === null) {
            return $difficulty;
        }

        $score = (float) $lastEvaluation->score;

        return match (true) {
            $score >= self::ADVANCE_THRESHOLD => $difficulty->increase(),
            $score <= self::REINFORCE_THRESHOLD => $difficulty->decrease(),
            default => $difficulty,
        };
    }

    private function harder(QuestionDifficulty $a, QuestionDifficulty $b): QuestionDifficulty
    {
        return $this->rank($a) >= $this->rank($b) ? $a : $b;
    }

    private function rank(QuestionDifficulty $difficulty): int
    {
        return match ($difficulty) {
            QuestionDifficulty::Easy => 1,
            QuestionDifficulty::Medium => 2,
            QuestionDifficulty::Hard => 3,
        };
    }

    private function baselineDifficulty(PositionLevel $level): QuestionDifficulty
    {
        return match ($level) {
            PositionLevel::Junior => QuestionDifficulty::Easy,
            PositionLevel::Mid => QuestionDifficulty::Medium,
            PositionLevel::Lead => QuestionDifficulty::Hard,
            PositionLevel::Senior => QuestionDifficulty::Medium,
        };
    }

    /**
     * @return array<int, string>
     */
    private function focusConcepts(?Evaluation $evaluation): array
    {
        if ($evaluation === null) {
            return [];
        }

        return array_slice(
            array_values(array_filter(array_map(
                static fn (mixed $concept): string => trim((string) $concept),
                $evaluation->missing_topics ?? [],
            ), static fn (string $concept): bool => $concept !== '')),
            0,
            self::MAX_FOCUS_CONCEPTS,
        );
    }
}
