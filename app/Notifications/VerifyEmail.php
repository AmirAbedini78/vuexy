<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmail extends Notification
{
    use Queueable;

    protected $link;

    /**
     * Create a new notification instance.
     */
    public function __construct($link)
    {
        $this->link = $link;
    }

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
        $frontendUrl = rtrim(config('app.frontend_url', env('APP_URL')), '/');

        $viewData = [
            'subject' => 'Verify Your Email Address',
            'heading' => 'Verify Your Email Address',
            'greeting_name' => $notifiable->name ?? 'there',
            'intro_html' => 'Hey ' . e($notifiable->name ?? 'there') . '<br><br>Thank you for starting the registration process with ' . e(config('app.name')) . '! To verify your email address and continue, please click the button below:',
            'primary_text' => 'Verify me!',
            'primary_url' => $this->link,
            'note_text' => "This link is valid for 24 hours. If you didn’t request this, please ignore this email or contact our support team.",
            'support_url' => 'https://explorerelite.com/support/',
            'whatsapp_url' => 'https://wa.me/',
            'frontend_url' => $frontendUrl,
        ];

        return (new MailMessage)
            ->subject('Verify Your Email Address')
            ->view('emails.verify-email', $viewData);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
