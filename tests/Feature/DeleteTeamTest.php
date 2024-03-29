<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Laravel\Jetstream\Http\Livewire\DeleteTeamForm;
use Livewire\Livewire;
use Tests\AbstractFeatureTest;

class DeleteTeamTest extends AbstractFeatureTest
{
    public function test_teams_can_be_deleted()
    {
        $this->markTestSkipped('Teams feature is not fully implemented yet.');

        /** @var User $user */
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        /** @var Team $team */
        $user->ownedTeams()->save($team = Team::factory()->make([
            'personal_team' => false,
        ]));

        $team->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'test-role']
        );

        Livewire::test(DeleteTeamForm::class, [
            'team' => $team->fresh(),
        ])->call('deleteTeam');

        $this->assertNull($team->fresh());
        $this->assertCount(0, $otherUser->fresh()->teams);
    }

    public function test_personal_teams_cant_be_deleted()
    {
        $this->markTestSkipped('Teams feature is not fully implemented yet.');

        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $component = Livewire::test(
            DeleteTeamForm::class,
            ['team' => $user->currentTeam]
        )
            ->call('deleteTeam')
            ->assertHasErrors(['team']);

        $this->assertNotNull($user->currentTeam->fresh());
    }
}
