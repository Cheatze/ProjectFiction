<?php

namespace App\Notifications;

use App\Models\Story;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewStoryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $story;

    /**
     * Create a new notification instance.
     */
    public function __construct(Story $story)
    {
        $this->story = $story;
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

        $url = route('stories.read', $this->story->id);

        $authorName = $this->story->user->name;

        return (new MailMessage)
            ->subject("New Story from {$authorName}!") // Email subject
            ->greeting("Hello {$notifiable->name},") // Greeting for the subscriber
            ->line("{$authorName} has just submitted a new story:")
            ->line("Title: " . $this->story->title)
            ->action('View Story', $url) // A call-to-action button
            ->line('Thank you for being a subscriber!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'story_id' => $this->story->id,
            'story_title' => $this->story->title,
            'story_url' => route('stories.read', $this->story->id),
            'author_name' => $this->story->user->name ?? 'A user',
        ];
    }
}
