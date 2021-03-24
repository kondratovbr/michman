<?php

namespace Tests\Feature;

use App\Http\Livewire\Profile\ChangeEmailForm;
use App\Models\User;
use Livewire\Livewire;
use Tests\AbstractFeatureTest;

class AccountEmailTest extends AbstractFeatureTest
{
    public function test_current_email_is_available()
    {
        $this->actingAs($user = User::factory()->create());

        $component = Livewire::test(ChangeEmailForm::class);

        $this->assertEquals($user->email, $component->email);
    }

    public function test_email_can_be_changed()
    {
        $this->actingAs($user = User::factory()->create());

        Livewire::test(ChangeEmailForm::class)
            ->set('email', 'test@example.com')
            ->call('changeEmail');

        $this->assertEquals('test@example.com', $user->fresh()->email);
    }
}
