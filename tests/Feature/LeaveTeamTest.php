<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Jetstream\Http\Livewire\TeamMemberManager;
use Livewire\Livewire;
use Tests\AbstractFeatureTest;

class LeaveTeamTest extends AbstractFeatureTest
{
    public function test_users_can_leave_teams()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $user->currentTeam->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        $this->actingAs($otherUser);

        $component = Livewire::test(TeamMemberManager::class, ['team' => $user->currentTeam])
            ->call('leaveTeam');

        $this->assertCount(0, $user->currentTeam->fresh()->users);
    }

    public function test_team_owners_cant_leave_their_own_team()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $component = Livewire::test(TeamMemberManager::class, ['team' => $user->currentTeam])
            ->call('leaveTeam')
            ->assertHasErrors(['team']);

        $this->assertNotNull($user->currentTeam->fresh());
    }
}
