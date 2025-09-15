<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ListingStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $status;
    protected ?string $listingTitle;

    public function __construct(string $status, ?string $listingTitle)
    {
        $this->status = $status; // submitted, approved, live, denied, edit_review, other_events, inactive
        $this->listingTitle = $listingTitle;
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

    protected function statusCopy(): array
    {
        return [
            'submitted' => [
                'title' => 'Submitted',
                'body' => 'Your submission has been successfully received. Please be patient until our team approve it.',
            ],
            'approved' => [
                'title' => 'Approved',
                'body' => "We've approved your adventure details, and it is now ready to be published.",
            ],
            'live' => [
                'title' => 'Live',
                'body' => 'Your adventure is active on the platform and participants can book it now.',
            ],
            'denied' => [
                'title' => 'Denied',
                'body' => 'Unfortunately, this submission did not meet our listing criteria; please check your email for more details or contact support.',
            ],
            'edit_review' => [
                'title' => 'Edit Review',
                'body' => 'The recent edits you made are pending review by our team before they go live.',
            ],
            'other_events' => [
                'title' => 'Other Events',
                'body' => "This is an external event you've added to your account for management purposes.",
            ],
            'inactive' => [
                'title' => 'Inactive',
                'body' => "The date for this adventure has passed or it’s just not active anymore.",
            ],
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $map = $this->statusCopy();
        $copy = $map[$this->status] ?? ['title' => 'Status Updated', 'body' => 'Your event status was updated.'];

        $frontendUrl = rtrim(config('app.frontend_url', env('APP_URL')), '/');

        $heading = match ($this->status) {
            'submitted' => 'Your Listing is Submitted',
            'approved' => 'Your Listing is Approved',
            'live' => 'Your Listing is Live',
            'denied' => 'Your Listing is Denied',
            'edit_review' => 'Your Listing is Under Review',
            default => 'Listing Status Updated',
        };

        $intro = ($this->listingTitle ? '“' . e($this->listingTitle) . '” — ' : '') . $copy['body'];

        $viewData = [
            'subject' => 'Event Status: ' . $copy['title'],
            'heading' => $heading,
            'intro_html' => e($intro),
            'primary_text' => 'Go To Dashboard',
            'primary_url' => $frontendUrl . '/',
            'secondary_text' => 'Contact Support',
            'secondary_url' => 'https://explorerelite.com/support/',
            'support_url' => 'https://explorerelite.com/support/',
            'whatsapp_url' => 'https://wa.me/',
            'frontend_url' => $frontendUrl,
        ];

        return (new MailMessage)
            ->subject('Event Status: ' . $copy['title'])
            ->view('emails.status-update', $viewData);
    }

    public function toArray(object $notifiable): array
    {
        $map = $this->statusCopy();
        $copy = $map[$this->status] ?? ['title' => 'Status Updated', 'body' => 'Your event status was updated.'];

        return [
            'type' => 'listing_status_updated',
            'status' => $this->status,
            'title' => $copy['title'],
            'message' => $copy['body'],
            'listing_title' => $this->listingTitle,
        ];
    }
}


