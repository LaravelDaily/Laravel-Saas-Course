<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class UserInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Invitation $invitation) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $organizationName = $this->invitation->organization?->name;

        return (new MailMessage)
            ->subject('You have been invited to join '.config('app.name'))
            ->greeting('Hello'.($this->invitation->name ? ' '.$this->invitation->name : '').'!')
            ->line('You have been invited to join '.($organizationName ?? 'our team').'.')
            ->action('Accept Invitation', URL::signedRoute('invitations.accept', $this->invitation, absolute: true))
            ->line('If you were not expecting this invitation, you can safely ignore this email.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'token' => $this->invitation->token,
            'email' => $this->invitation->email,
            'organization_id' => $this->invitation->organization_id,
        ];
    }
}
