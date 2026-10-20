<?php

namespace Tests\Feature;

use App\Livewire\AccountSettings;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AccountSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create(['password' => 'secret-password']);
        $user->assignRole($role);

        return $user;
    }

    #[Test]
    public function settings_screen_renders_for_any_authenticated_role(): void
    {
        $member = $this->userWithRole('team-member');

        $this->actingAs($member)->get('/settings')
            ->assertOk()
            ->assertSee('Settings')
            ->assertSee($member->email);
    }

    #[Test]
    public function guest_is_redirected_to_login(): void
    {
        $this->get('/settings')->assertRedirect('/login');
    }

    #[Test]
    public function profile_can_be_updated(): void
    {
        $member = $this->userWithRole('team-member');

        Livewire::actingAs($member)
            ->test(AccountSettings::class)
            ->set('name', 'Fajar Nugroho, S.T.')
            ->set('email', 'fajar@upnyk.ac.id')
            ->call('updateProfile')
            ->assertHasNoErrors();

        $member->refresh();

        $this->assertSame('Fajar Nugroho, S.T.', $member->name);
        $this->assertSame('fajar@upnyk.ac.id', $member->email);
    }

    #[Test]
    public function profile_email_must_stay_unique(): void
    {
        $member = $this->userWithRole('team-member');
        $other = $this->userWithRole('viewer');

        Livewire::actingAs($member)
            ->test(AccountSettings::class)
            ->set('email', $other->email)
            ->call('updateProfile')
            ->assertHasErrors('email');

        $this->assertNotSame($other->email, $member->refresh()->email);
    }

    #[Test]
    public function password_requires_the_current_password(): void
    {
        $member = $this->userWithRole('team-member');

        Livewire::actingAs($member)
            ->test(AccountSettings::class)
            ->set('current_password', 'wrong-password')
            ->set('password', 'new-password-123')
            ->set('password_confirmation', 'new-password-123')
            ->call('updatePassword')
            ->assertHasErrors('current_password');

        $this->assertTrue(Hash::check('secret-password', $member->refresh()->password));
    }

    #[Test]
    public function password_can_be_changed(): void
    {
        $member = $this->userWithRole('team-member');

        Livewire::actingAs($member)
            ->test(AccountSettings::class)
            ->set('current_password', 'secret-password')
            ->set('password', 'new-password-123')
            ->set('password_confirmation', 'new-password-123')
            ->call('updatePassword')
            ->assertHasNoErrors();

        $this->assertTrue(Hash::check('new-password-123', $member->refresh()->password));
    }
}
