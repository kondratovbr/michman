<?php declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Actions\OAuthUsers\CreateOAuthUserAction;
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
    public function __construct(
        protected CreateOAuthUserAction $createOAuthUserAction,
    ) {}

    use PasswordValidationRules;

    /** Create a new user. */
    public function create(array $input): User
    {
        $validated = Validator::make($input, [
            'email' => Rules::email()
                ->doesNotExistInDb('users', 'email')
                ->required(),
            'password' => $this->passwordRules(),
            // TODO: IMPORTANT! I should update these OAuth rules and make sure I notify myself if they fail - they shouldn't.
            'oauth_provider' => Rules::string(1, 255),
            'oauth_id' => Rules::string(1, 255),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ])->validate();

        return DB::transaction(function () use ($validated) {
            /** @var User $user */
            $user = User::create([
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            if (! empty($validated['oauth_provider'])) {
                $this->createOAuthUserAction->execute(
                    $validated['oauth_provider'],
                    $validated['oauth_id'],
                    $user,
                );
            }

            $this->createTeam($user);

            return $user;
        }, 5);
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
