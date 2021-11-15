<?php declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Models\Team;
use App\Models\User;
use App\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        $validated = Validator::make($input, [
            'email' => Rules::email()
                ->doesNotExistInDb('users', 'email')
                ->required(),
            'password' => $this->passwordRules(),
            'oauth_provider' => Rules::string(1, 255),
            'oauth_id' => Rules::string(1, 255),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ])->validate();

        return DB::transaction(function () use ($validated) {

            if (empty($validated['oauth_provider'])) {
                $user = User::create([
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                ]);
            } else {
                $user = User::create([
                    'email' => $validated['email'],
                    'oauth_provider' => $validated['oauth_provider'],
                    'oauth_id' => $validated['oauth_id'],
                ]);
            }

            $this->createTeam($user);

            return $user;

        });
    }

    /** Create a personal team for the user. */
    protected function createTeam(User $user): void
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => $user->getNameForPersonalTeam(),
            'personal_team' => true,
        ]));
    }
}
