<?php

namespace App\Listeners;


use App\Events\StoryPosted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Story;
use App\Models\User;
use App\Notifications\NewStoryNotification;

// use Illuminate\Support\Facades\Mail;
// use Illuminate\Notifications\Notification;
// use Illuminate\Notifications\Messages\MailMessage;

class SubscriberNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'notifications';
    public $tries = 5;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StoryPosted $event): void
    {
        $story = $event->story;
        $author = $story->user;

        // Check if the author exists and has subscribers
        if ($author) {
            //Sends the notification to all subscribed to that author
            $author->subscribers->each(fn($subscriber) => $subscriber->notify(new NewStoryNotification($story)));

        }
    }
}
