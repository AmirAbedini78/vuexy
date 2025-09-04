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
        return ['mail', 'database'];
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

        return (new MailMessage)
            ->subject('Event Status: ' . $copy['title'])
            ->greeting('Hello ' . ($notifiable->name ?? ''))
            ->line(($this->listingTitle ? '“' . $this->listingTitle . '” — ' : '') . $copy['body'])
            ->action('View your events', url(config('app.frontend_url', env('APP_URL'))))
            ->line('Thank you for using our platform!');
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


