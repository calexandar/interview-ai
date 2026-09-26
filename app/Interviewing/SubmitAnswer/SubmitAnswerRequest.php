<?php

namespace App\Interviewing\SubmitAnswer;

use Illuminate\Foundation\Http\FormRequest;

class SubmitAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'question_id' => ['required', 'integer'],
            'content' => ['required', 'string', 'min:1', 'max:'.config('interview.answer.max_chars', 10000)],
            'duration_seconds' => ['nullable', 'integer', 'min:0', 'max:86400'],
        ];
    }

    public function toCommand(): SubmitAnswer
    {
        return new SubmitAnswer(
            interviewId: (int) $this->route('interview'),
            questionId: (int) $this->input('question_id'),
            organizationId: (int) $this->user()->organization_id,
            content: (string) $this->input('content'),
            durationSeconds: $this->input('duration_seconds') === null
                ? null
                : (int) $this->input('duration_seconds'),
        );
    }
}
