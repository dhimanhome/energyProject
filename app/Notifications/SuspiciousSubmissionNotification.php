<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class SuspiciousSubmissionNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Submission $submission)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Suspicious power reading submission')
            ->line($this->submission->employee?->name.' submitted a '.$this->submission->risk_level.' reading.')
            ->line('Site: '.$this->submission->site?->site_name)
            ->line('Distance: '.$this->submission->distance_from_site.' meters')
            ->action('Review submission', route('submissions.show', $this->submission));
    }

    public function sendTelegram(): void
    {
        if (! config('services.telegram.bot_token') || ! config('services.telegram.alert_chat_id')) {
            return;
        }

        Http::post('https://api.telegram.org/bot'.config('services.telegram.bot_token').'/sendMessage', [
            'chat_id' => config('services.telegram.alert_chat_id'),
            'text' => sprintf(
                "Suspicious reading\nEmployee: %s\nSite: %s\nDistance: %sm",
                $this->submission->employee?->name,
                $this->submission->site?->site_name,
                $this->submission->distance_from_site
            ),
        ]);
    }
}
