<?php

namespace Tests\Feature;

use App\Http\Livewire\Profile\DeleteAccountForm;
use App\Models\User;
use Laravel\Jetstream\Features;
use Livewire\Livewire;
use Tests\AbstractFeatureTest;

class DeleteAccountFormTest extends AbstractFeatureTest
{
    public function test_user_accounts_can_be_deleted()
    {
        $this->markTestSkipped('Account deletion is temporarily disabled.');

        if (! Features::hasAccountDeletionFeatures()) {
            $this->markTestSkipped('Account deletion is not enabled.');
        }

        $this->actingAs($user = User::factory()->create());

        $component = Livewire::test(DeleteAccountForm::class)
            ->set('password', 'password')
            ->call('deleteUser');

        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_before_account_can_be_deleted()
    {
        $this->markTestSkipped('Account deletion is temporarily disabled.');

        if (! Features::hasAccountDeletionFeatures()) {
            $this->markTestSkipped('Account deletion is not enabled.');
        }

        $this->actingAs($user = User::factory()->create());

        Livewire::test(DeleteAccountForm::class)
            ->set('password', 'wrong-password')
            ->call('deleteUser')
            ->assertHasErrors(['password']);

        $this->assertNotNull($user->fresh());
    }
}
