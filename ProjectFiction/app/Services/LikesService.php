<?php

namespace App\Services;

use App\Models\Story;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class LikesService
{
    /**
     * Toggles a user's like on a story.
     *
     * @param Story $story
     * @param User $user
     * @return bool
     */
    public function toggleLike(Story $story, User $user): bool
    {
        if ($story->likers()->where('user_id', $user->id)->exists()) {
            return $this->unlike($story, $user);
        } else {
            return $this->like($story, $user);
        }
    }

    /**
     * Like a story for a given user.
     *
     * @param Story $story
     * @param User $user
     * @return bool
     */
    public function like(Story $story, User $user): bool
    {
        // Check if the user has already liked the story.
        if ($story->likers()->where('user_id', $user->id)->exists()) {
            Log::channel('liking')->info('User attempted to like a story they already liked', ['user_id' => $user->id, 'story_id' => $story->id]);
            return false;
        }

        // Attach the user to the story's 'likers' relationship.
        $story->likers()->attach($user->id);
        Log::channel('liking')->info('User liked a story', ['user_id' => $user->id, 'story_id' => $story->id]);

        // Increment the 'likes' and 'score' fields on the story.
        $story->increment('likes');
        $story->increment('score', 10);
        Log::channel('liking')->info('Story likes and score incremented', ['story_id' => $story->id, 'new_likes' => $story->likes, 'new_score' => $story->score]);

        return true;
    }

    /**
     * Unlike a story for a given user.
     *
     * @param Story $story
     * @param User $user
     * @return bool
     */
    public function unlike(Story $story, User $user): bool
    {
        // Check if the user has not already liked the story.
        if (!$story->likers()->where('user_id', $user->id)->exists()) {
            Log::channel('liking')->info('User attempted to unlike a story they have not liked', ['user_id' => $user->id, 'story_id' => $story->id]);
            return false;
        }

        // Detach the user from the story's 'likers' relationship.
        $story->likers()->detach($user->id);
        Log::channel('liking')->info('User unliked a story', ['user_id' => $user->id, 'story_id' => $story->id]);

        // Decrement the 'likes' and 'score' fields on the story.
        $story->decrement('likes');
        $story->decrement('score', 10);
        Log::channel('liking')->info('Story likes and score decremented', ['story_id' => $story->id, 'new_likes' => $story->likes, 'new_score' => $story->score]);

        return true;
    }
}
