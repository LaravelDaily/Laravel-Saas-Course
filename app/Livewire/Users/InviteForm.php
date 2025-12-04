<?php

namespace App\Livewire\Users;

use App\Enums\RoleEnum;
use App\Models\Invitation;
use App\Notifications\UserInvitationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class InviteForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $role = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->hasPermissionTo('users.create'), 403);

        $this->role = RoleEnum::User->value;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users',
                Rule::unique('invitations', 'email')->where('organization_id', auth()->user()?->organization_id),
            ],
            'role' => ['required', Rule::enum(RoleEnum::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please provide the invitee name.',
            'email.unique' => 'The provided email address already has access or a pending invitation.',
            'role.required' => 'Please select a role.',
            'role.in' => 'Invalid role selected.',
        ];
    }

    public function submit(): void
    {
        $currentUser = auth()->user();

        abort_unless($currentUser->hasPermissionTo('users.create'), 403);

        $this->validate();

        $invitation = Invitation::create([
            'organization_id' => $currentUser->organization_id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'token' => Str::uuid()->toString(),
        ]);

        Notification::route('mail', $invitation->email)
            ->notify(new UserInvitationNotification($invitation));

        session()->flash('success', 'Invitation sent successfully.');

        $this->redirect(route('users.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.users.invite-form');
    }
}
