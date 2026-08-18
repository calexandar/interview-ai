<?php

namespace App\Candidates\CreateCandidate;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCandidateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('candidates', 'email')->where(fn ($query) => $query->where('organization_id', $this->user()->organization_id)),
            ],
        ];
    }

    public function toCommand(): CreateCandidate
    {
        return new CreateCandidate(
            organizationId: $this->user()->organization_id,
            name: $this->validated('name'),
            email: $this->validated('email'),
        );
    }
}
