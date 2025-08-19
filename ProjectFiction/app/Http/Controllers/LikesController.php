<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Story;
use Illuminate\Support\Facades\Auth;

class LikesController extends Controller
{
    /**
     * Like a story.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function like(Story $story)
    {
        // Check if the authenticated user has already liked the story
        if ($story->likers()->where('user_id', Auth::user()->id)->exists()) {
            return back()->with('error', 'You have already liked this story.');
        }

        // Attach the user to the story's 'likers' relationship
        $story->likers()->attach(Auth::user()->id);

        // Increment the 'likes' field on the story
        $story->increment('likes');
        $story->increment('score', 10); // Increment score by 10 for each like

        return redirect()->back()->with('success', 'Story liked successfully!');
    }
}
