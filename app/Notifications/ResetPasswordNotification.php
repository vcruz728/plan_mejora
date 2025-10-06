<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('Restablecer tu contraseña')
            ->greeting('Hola ' . ($notifiable->name ?? ''))
            ->line('Recibiste este correo porque se solicitó restablecer tu contraseña.')
            ->action('Restablecer contraseña', $this->url)
            ->line('Si no solicitaste este cambio, ignora este correo.')
            ->salutation('— Plataforma de planes de mejora');
    }
}
