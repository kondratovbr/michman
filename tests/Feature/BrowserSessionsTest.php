<?php

namespace Tests\Feature;

use App\Http\Livewire\Profile\LogoutSessionsForm;
use App\Models\User;
use Livewire\Livewire;
use Tests\AbstractFeatureTest;

class BrowserSessionsTest extends AbstractFeatureTest
{
    public function test_other_browser_sessions_can_be_logged_out()
    {
        $this->actingAs($user = User::factory()->create());

        Livewire::test(LogoutSessionsForm::class)
                ->set('password', 'password')
                ->call('logoutOtherSessions');
    }
}
