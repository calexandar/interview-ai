<?php

namespace App\Positions\CreatePosition;

use App\Shared\Enums\PositionLevel;
use App\Shared\Enums\PositionStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePositionRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'level' => ['required', Rule::enum(PositionLevel::class)],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:180'],
            'question_count' => ['required', 'integer', 'min:1', 'max:30'],
            'status' => ['sometimes', Rule::enum(PositionStatus::class)],
            'skill_ids' => ['sometimes', 'array'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
        ];
    }

    public function toCommand(): CreatePosition
    {
        return new CreatePosition(
            organizationId: $this->user()->organization_id,
            title: $this->validated('title'),
            description: $this->validated('description'),
            level: PositionLevel::from($this->validated('level')),
            durationMinutes: $this->validated('duration_minutes'),
            questionCount: $this->validated('question_count'),
            status: PositionStatus::from($this->validated('status', PositionStatus::Active->value)),
            skillIds: $this->validated('skill_ids'),
        );
    }
}
