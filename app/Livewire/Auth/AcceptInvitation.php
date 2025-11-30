<?php

namespace App\Livewire\Auth;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class AcceptInvitation extends Component
{
    public Invitation $invitation;

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(Invitation $invitation): void
    {
        if ($invitation->accepted_at !== null) {
            abort(404);
        }

        if (User::where('email', $invitation->email)->exists()) {
            abort(404);
        }

        $this->invitation = $invitation;
    }

    public function accept(): void
    {
        $validated = $this->validate([
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create([
            'name' => $this->invitation->name,
            'email' => $this->invitation->email,
            'password' => $validated['password'],
            'organization_id' => $this->invitation->organization_id,
            'is_admin' => $this->invitation->is_admin,
        ]);

        $this->invitation->update(['accepted_at' => now()]);

        event(new Registered($user));

        Auth::login($user);

        Session::regenerate();

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.accept-invitation');
    }
}
