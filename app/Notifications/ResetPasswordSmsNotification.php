<?php

namespace App\Notifications;

use App\Notifications\Channels\LogSmsChannel;
use Illuminate\Notifications\Notification;

class ResetPasswordSmsNotification extends Notification
{
    public function __construct(private readonly string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return [LogSmsChannel::class];
    }

    public function toSms(object $notifiable): string
    {
        $login = $notifiable->phone ?? '';
        $resetUrl = url('/reset-password/' . $this->token . '?login=' . urlencode((string) $login));

        return "Evolua: use este token para redefinir sua senha: {$this->token}. Link: {$resetUrl}";
    }
}
