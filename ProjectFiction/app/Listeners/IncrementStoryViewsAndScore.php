<?php

namespace App\Listeners;

use App\Events\StoryViewed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class IncrementStoryViewsAndScore
{
    //use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Increments the views and score of a story when it is viewed.
     */
    public function handle(StoryViewed $event): void
    {
        $story = $event->story;
        $sessionKey = 'story_viewed_' . $story->id;

        if (!Session::has($sessionKey)) {
            Log::channel('stories')->info('Incrementing views and score', ['story_id' => $story->id]);

            $story->increment('views');
            $story->increment('score');

            Session::put($sessionKey, true);

            Log::channel('stories')->info('Views and score updated successfully', ['story_id' => $story->id, 'new_views' => $story->views, 'new_score' => $story->score]);
        } else {
            Log::channel('stories')->info('Story already viewed this session', ['story_id' => $story->id]);
        }
    }
}
