<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ResetPassword extends Notification
{
    public $token;
    public $url;

    public function __construct($token, $url)
    {
        $this->token = $token;
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $data = [
            'subject' => 'Reset Your Password - ' . config('app.name'),
            'title' => 'Reset Your Password',
            'body' => "Hey {$notifiable->name}<br><br>
                      We received a request to reset your password for your " . config('app.name') . " account. 
                      To reset your password and continue, please click the button below:",
            'actionUrl' => $this->url,
            'actionText' => 'Reset Password',
            'importantNote' => "This password reset link will expire in " . config('auth.passwords.users.expire') . " minutes. 
                               If you didn't request a password reset, please ignore this email or contact our support team."
        ];

        return (new MailMessage)
            ->subject($data['subject'])
            ->view('emails.base-template', $data);
    }
}
