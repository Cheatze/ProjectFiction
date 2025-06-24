<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Story;
use Illuminate\Auth\Access\Response;

class StoryPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }


    public function delete(User $user, Story $story)
    {
        // echo 'user: ' . $user->id;
        // echo 'story: ' . $story->user_id;
        //dd($story);
        return $user->id == $story->user_id
            ? Response::allow()
            : Response::deny('You do not own this story.');
        // A user can delete a story if their ID matches the story's user_id.
        // return $user->id === $story->user_id;
        // ? Response::allow()
        // : Response::deny('You do not own this story.'); // Optional custom message
    }


}
