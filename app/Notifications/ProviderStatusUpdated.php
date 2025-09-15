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

        $frontendUrl = rtrim(config('app.frontend_url', env('APP_URL')), '/');

        $viewData = [
            'subject' => $title,
            'heading' => 'Your Listing is Under Review',
            'intro_html' => sprintf(
                'Great news! Your %s, <strong>%s</strong>, is under %s by our team, we’ll notify you once it’s done. You can always contact us if you need to make changes.',
                e($this->providerType === 'company' ? 'Company Profile' : 'Profile'),
                e($this->providerName ?? 'account'),
                e($this->status === 'active' ? 'activation' : 'review')
            ),
            'primary_text' => 'Go To Dashboard',
            'primary_url' => $frontendUrl . '/',
            'secondary_text' => 'Contact Support',
            'secondary_url' => 'https://explorerelite.com/support/',
            'support_url' => 'https://explorerelite.com/support/',
            'whatsapp_url' => 'https://wa.me/',
            'frontend_url' => $frontendUrl,
        ];

        return (new MailMessage)
            ->subject($title)
            ->view('emails.status-update', $viewData);
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


