<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Settings', 'breadcrumb' => 'Settings'])]
class AccountSettings extends Component
{
    public string $name = '';

    public string $email = '';

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        $user = auth()->user();

        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function updateProfile(): void
    {
        $user = auth()->user();

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->update($data);

        session()->flash('success', 'Profile updated.');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update(['password' => $this->password]);

        $this->reset('current_password', 'password', 'password_confirmation');

        session()->flash('success', 'Password updated.');
    }

    public function render(): View
    {
        /** @var User $user */
        $user = auth()->user();

        return view('livewire.account-settings', [
            'user' => $user,
            'roleMeta' => $user->roleMeta(),
            'securityStatus' => $user->securityStatus(),
        ]);
    }
}
