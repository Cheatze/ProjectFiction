<?php

namespace App\Services;

use App\Models\Story;
use App\Models\User;
use App\Enums\Genre;
use App\Events\StoryPosted;
use App\Events\StoryViewed;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class StoryService
{

    /**
     * Get a paginated list of stories from new to old.
     */
    public function getNewStories(): LengthAwarePaginator
    {
        return Story::withAuthor()->latest()->paginate(15);
    }

    /**
     * Get a paginated list of stories ordered by score (e.g., likes).
     */
    public function getPopularStories(): LengthAwarePaginator
    {
        // You would need a method to order by score/likes
        // For now, let's assume a placeholder logic
        return Story::Popular()->paginate(15);
    }

    /**
     * Get a paginated list of stories by a specific genre.
     */
    public function getStoriesByGenre(Genre $genre): LengthAwarePaginator
    {
        return Story::withAuthor()
            ->where('genre', $genre->value)
            ->paginate(15);
    }

    /**
     * Search for stories by a given search term.
     */
    public function searchStories(string $searchTerm): LengthAwarePaginator
    {
        return Story::withAuthor()
            ->where('title', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('synopsis', 'LIKE', '%' . $searchTerm . '%')
            ->paginate(15);
    }

    /**
     * Get a paginated list of stories for a specific user.
     *
     * @param User $user
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUserStories(User $user)
    {
        return $user->stories()
            ->select('id', 'title', 'genre', 'synopsis')
            ->orderBy('id', 'desc')
            ->paginate(15);
    }

    /**
     * Retrieve a single story by its ID.
     */
    public function getStory(Story $story): Story
    {
        event(new StoryViewed($story));
        $story->content = Purify::clean($story->content);
        return $story;
    }

    /**
     * Check if a user has liked a specific story.
     */
    public function hasUserLikedStory(Story $story, ?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $story->likers()->where('user_id', $user->id)->exists();
    }

    /**
     * Create and save a new story.
     */
    public function createStory(array $data, User $user): Story
    {
        $story = new Story();
        $story->title = $data['title'];
        $story->synopsis = $data['synopsis'];
        $story->genre = $data['genre'];
        $story->content = Purify::config('story')->clean($data['story']);
        $story->user_id = $user->id;
        $story->save();

        event(new StoryPosted($story));

        return $story;
    }

    /**
     * Delete a story.
     */
    public function deleteStory(Story $story): bool
    {
        return $story->forceDelete();
    }

    /**
     * Get a random story.
     */
    public function getRandomStory(): ?Story
    {
        return Story::inRandomOrder()->first();
    }

}
