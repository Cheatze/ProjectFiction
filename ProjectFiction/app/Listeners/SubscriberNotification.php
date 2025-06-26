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

            $author->subscribers->each(fn($subscriber) => $subscriber->notify(new NewStoryNotification($story)));

            // Get all users who are subscribed to this author
            // $author->subscribers() is the relationship defined in the User model
            // $subscribers = $author->subscribers; // This returns a collection of User models

            // foreach ($subscribers as $subscriber) {
            //     Send the notification to each subscriber
            //     $subscriber->notify(new NewStoryNotification($story));
            // }
        }
    }
}
