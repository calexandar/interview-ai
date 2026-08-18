<?php

namespace App\Interviewing\CreateInterview;

use App\Shared\Enums\InterviewType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateInterviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'position_id' => ['required', 'integer', 'exists:positions,id'],
            'candidate_id' => ['required', 'integer', 'exists:candidates,id'],
            'type' => ['required', Rule::enum(InterviewType::class)],
        ];
    }

    public function toCommand(): CreateInterview
    {
        return new CreateInterview(
            organizationId: $this->user()->organization_id,
            positionId: $this->validated('position_id'),
            candidateId: $this->validated('candidate_id'),
            type: InterviewType::from($this->validated('type')),
        );
    }
}
