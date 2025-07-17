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
        $data = [
            'subject' => 'Verify Your Email Address - ' . config('app.name'),
            'title' => 'Verify Your Email Address',
            'body' => "Hey {$notifiable->name}<br><br>
                      Thank you for starting the registration process with " . config('app.name') . "! 
                      To verify your email address and continue, please click the button below:",
            'actionUrl' => $this->link,
            'actionText' => 'Verify me!',
            'importantNote' => "This link is valid for 24 hours. If you didn't request this, please ignore this email or contact our support team."
        ];

        return (new MailMessage)
            ->subject($data['subject'])
            ->view('emails.base-template', $data);
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
