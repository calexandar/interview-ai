<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'company' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
        ])->validate();

        $organization = Organization::create([
            'name' => $input['company'],
            'slug' => Str::slug($input['company']),
            'status' => 'active',
        ]);

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'] ?? null,
            'password' => $input['password'],
            'organization_id' => $organization->id,
        ]);
    }
}
