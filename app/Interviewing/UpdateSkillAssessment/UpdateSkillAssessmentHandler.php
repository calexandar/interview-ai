<?php

namespace App\Interviewing\UpdateSkillAssessment;

use App\Models\Answer;
use App\Models\Evaluation;
use App\Models\Interview;
use App\Models\SkillAssessment;
use App\Shared\Enums\EvaluationStatus;
use App\Shared\Enums\QuestionStatus;
use Illuminate\Support\Collection;

/**
 * Recomputes a skill's assessment from every answer evaluated for it.
 *
 * The formula is plain PHP over settled evaluations. Nothing is asked of the
 * model, so the same interview always produces the same number, the number can
 * be reproduced from the stored evidence, and a reviewer can check the maths by
 * hand.
 *
 * A skill with no settled evidence is left alone rather than given a fabricated
 * zero-with-confidence score.
 */
class UpdateSkillAssessmentHandler
{
    public function handle(UpdateSkillAssessment $command): ?SkillAssessment
    {
        $interview = Interview::where('id', $command->interviewId)->firstOrFail();

        $evaluations = $this->settledEvaluations($interview, $command->skillId);

        if ($evaluations->isEmpty()) {
            return $this->existing($interview, $command->skillId);
        }

        $scores = $evaluations->map(fn (Evaluation $evaluation): float => (float) $evaluation->score);
        $confidences = $evaluations->map(fn (Evaluation $evaluation): float => (float) $evaluation->confidence);

        $evidence = $evaluations
            ->flatMap(fn (Evaluation $evaluation): array => $evaluation->evidence ?? [])
            ->filter(static fn (mixed $item): bool => is_string($item) && trim($item) !== '')
            ->map(static fn (string $item): string => trim($item))
            ->unique()
            ->values()
            ->all();

        return SkillAssessment::updateOrCreate(
            [
                'interview_id' => $interview->id,
                'skill_id' => $command->skillId,
            ],
            [
                'score' => round((float) $scores->avg(), 1),
                'confidence' => round((float) $confidences->avg(), 2),
                'questions_answered' => $evaluations->count(),
                'evidence' => $evidence,
            ],
        );
    }

    /**
     * Every completed evaluation attached to a question that actually targeted
     * this skill. Failed and never-attempted evaluations are excluded so a
     * provider outage can never move a score.
     *
     * @return Collection<int, Evaluation>
     */
    private function settledEvaluations(Interview $interview, int $skillId): Collection
    {
        $answerIds = Answer::query()
            ->whereHas('interviewQuestion', function ($query) use ($interview, $skillId): void {
                $query->where('interview_id', $interview->id)
                    ->where('skill_id', $skillId)
                    ->where('status', QuestionStatus::Answered);
            })
            ->pluck('id');

        if ($answerIds->isEmpty()) {
            return collect();
        }

        return Evaluation::query()
            ->whereIn('answer_id', $answerIds)
            ->where('status', EvaluationStatus::Completed)
            ->orderBy('id')
            ->get();
    }

    private function existing(Interview $interview, int $skillId): ?SkillAssessment
    {
        return SkillAssessment::where('interview_id', $interview->id)
            ->where('skill_id', $skillId)
            ->first();
    }
}
