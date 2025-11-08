<?php

namespace App\Admin\Actions\Fortify;

use App\Models\AdminUser;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): AdminUser
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(AdminUser::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        return AdminUser::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);
    }
}
