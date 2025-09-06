<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProviderStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $status;
    protected string $providerType;
    protected ?string $providerName;

    public function __construct(string $status, string $providerType, ?string $providerName)
    {
        $this->status = $status; // active | approved | rejected
        $this->providerType = $providerType; // individual | company
        $this->providerName = $providerName;
    }

    public function via(object $notifiable): array
    {
        $channels = ['mail'];
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                $channels[] = 'database';
            }
        } catch (\Throwable $e) {
            // Ignore and fallback to mail only
        }
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $title = 'Your provider status was updated';
        $line = sprintf(
            'Your %s profile "%s" has been %s by admin.',
            $this->providerType,
            $this->providerName ?? 'account',
            $this->status
        );

        return (new MailMessage)
            ->subject($title)
            ->greeting('Hello ' . ($notifiable->name ?? ''))
            ->line($line)
            ->action('View your profile', url(config('app.frontend_url', env('APP_URL'))))
            ->line('Thank you for using our platform!');
    }

    public function toArray(object $notifiable): array
    {
        // Used by database channel
        return [
            'type' => 'provider_status_updated',
            'title' => 'Provider status updated',
            'subtitle' => sprintf(
                'Your %s "%s" is now %s',
                $this->providerType,
                $this->providerName ?? 'account',
                $this->status
            ),
            'status' => $this->status,
            'provider_type' => $this->providerType,
            'provider_name' => $this->providerName,
        ];
    }
}


