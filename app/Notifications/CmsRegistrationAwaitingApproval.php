<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CmsRegistrationAwaitingApproval extends Notification
{
    public function __construct(public User $registeredUser) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('CMS account awaiting approval')
            ->greeting('A CMS registration has verified its email address.')
            ->line('Name: '.$this->registeredUser->name)
            ->line('Email: '.$this->registeredUser->email)
            ->action('Review registration', route('admin.users.index'));
    }
}
