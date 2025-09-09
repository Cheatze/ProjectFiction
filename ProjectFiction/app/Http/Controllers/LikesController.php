<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Story;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\LikesService;

class LikesController extends Controller
{
    // /**
    //  * Like a story.
    //  *
    //  * @param int $id
    //  * @return \Illuminate\Http\RedirectResponse
    //  */
    // public function like(Story $story, LikesService $likesService)
    // {
    //     // Check if the authenticated user has already liked the story
    //     if ($story->likers()->where('user_id', Auth::user()->id)->exists()) {
    //         Log::channel('liking')->info('User attempted to like a story they already liked', ['user_id' => Auth::user()->id, 'story_id' => $story->id]);
    //         return back()->with('error', 'You have already liked this story.');
    //     }

    //     // Attach the user to the story's 'likers' relationship
    //     $story->likers()->attach(Auth::user()->id);
    //     Log::channel('liking')->info('User liked a story', ['user_id' => Auth::user()->id, 'story_id' => $story->id]);

    //     // Increment the 'likes' field on the story
    //     $story->increment('likes');
    //     $story->increment('score', 10); // Increment score by 10 for each like
    //     Log::channel('liking')->info('Story likes and score incremented', ['story_id' => $story->id, 'new_likes' => $story->likes, 'new_score' => $story->score]);

    //     return redirect()->back()->with('success', 'Story liked successfully!');
    // }

    // /**
    //  * Unlike a story.
    //  *
    //  * @param int $id
    //  * @return \Illuminate\Http\RedirectResponse
    //  */
    // public function unlike(Story $story)
    // {
    //     // Check if the authenticated user has liked the story
    //     if (!$story->likers()->where('user_id', Auth::user()->id)->exists()) {
    //         Log::channel('liking')->info('User attempted to unlike a story they have not liked', ['user_id' => Auth::user()->id, 'story_id' => $story->id]);
    //         return back()->with('error', 'You have not liked this story yet.');
    //     }
    //     // Detach the user from the story's 'likers' relationship
    //     $story->likers()->detach(Auth::user()->id);
    //     Log::channel('liking')->info('User unliked a story', ['user_id' => Auth::user()->id, 'story_id' => $story->id]);

    //     // Decrement the 'likes' field on the story
    //     $story->decrement('likes');
    //     $story->decrement('score', 10); // Decrement score by 10 for each unlike
    //     Log::channel('liking')->info('Story likes and score decremented', ['story_id' => $story->id, 'new_likes' => $story->likes, 'new_score' => $story->score]);

    //     return redirect()->back()->with('success', 'Story unliked successfully!');
    // }

    /**
     * Toggles a user's like on a story.
     *
     * @param Story $story
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggle(Story $story, LikesService $likesService)
    {
        $user = Auth::user();
        $toggled = $likesService->toggleLike($story, $user);

        if ($toggled) {
            return back()->with('success', 'Like status updated successfully!');
        } else {
            return back()->with('error', 'There was an issue updating your like status.');
        }
    }
}
