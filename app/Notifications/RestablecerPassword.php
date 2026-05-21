<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RestablecerPassword extends Notification
{
    use Queueable;

    public $token;

    /**
     * Crear una nueva instancia de la notificación
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Canal de envío
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Formatear el mensaje de correo
     */
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Restablecer contraseña - PAI')
            ->view('emails.restablecer', [
                'url' => $url,
                'user' => $notifiable,
            ]);
    }
}
