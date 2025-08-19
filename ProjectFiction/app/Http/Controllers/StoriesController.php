<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Events\StoryPosted;
use App\Enums\Genre;
use Illuminate\Validation\Rules\Enum;
use App\Http\Requests\SubmitStoryRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\DeleteRequest;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class StoriesController extends Controller
{
    public function showWrite()
    {
        Log::channel('stories')->info('User accessed the write story page', ['user_id' => Auth::id()]);
        return view('write');
    }

    /**
     * 
     * Shows a paginated list of stories from new to old
     * @return \Illuminate\Contracts\View\View
     */
    public function showNew()
    {
        $list = Story::withAuthor()->paginate(15);

        Log::channel('stories')->info('Showing new stories', ['count' => $list->count()]);

        return view('browse')->with('stories', $list);
    }

    /**
     * 
     * Shows a paginated list of stories ordered by score
     * @return \Illuminate\Contracts\View\View
     */
    public function showPopular()
    {
        $list = Story::Popular()->paginate(15);

        Log::channel('stories')->info('Showing popular stories', ['count' => $list->count()]);

        return view('browse')->with('stories', $list);
    }

    /**
     * 
     * Returns a paginated view of stories with a certain genre using an enum
     * @param \app\Enums\Genre $genre
     * @return \Illuminate\Contracts\View\View
     */
    public function showGenre(Genre $genre)
    {
        $theGenre = $genre->value;

        $list = Story::withAuthor()
            ->where('genre', $theGenre)
            ->paginate(15);

        log::channel('stories')->info('Showing stories by genre', ['genre' => $theGenre, 'count' => $list->count()]);

        return view('browse')->with('stories', $list);
    }

    public function showSearch(SearchRequest $request)
    {
        //$validator = $request->validated();
        $searchTerm = $request->input('search');

        log::channel('stories')->info('User searched for stories', ['search_term' => $searchTerm]);

        $list = Story::withAuthor() // Start a new query builder instance for the Story model
            ->where('title', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('synopsis', 'LIKE', '%' . $searchTerm . '%')
            ->paginate(15);

        log::channel('stories')->info('Search results count', ['count' => $list->count()]);

        return view('browse')->with('stories', $list);
    }

    /**
     * Returns a view with the content of a certain story
     * @param mixed $id
     * @return \Illuminate\Contracts\View\View
     */
    public function showStory($id)
    {
        log::channel('stories')->info('User accessed story reading page', ['story_id' => $id]);

        //Could possibly be replaced with type casting but I get errors when I try
        $story = Story::where('id', $id)->first();

        if (!$story) {
            log::channel('stories')->error('Story not found', ['story_id' => $id]);
            abort(404); // Or handle the error as needed
        }

        log::channel('stories')->info('Story content retrieved', ['story_id' => $id]);

        // Check if the story has already been viewed in this session
        $sessionKey = 'story_viewed_' . $story->id;

        if (!Session::has($sessionKey)) {
            // Log the view and score increment
            Log::channel('stories')->info('Incrementing views and score', ['story_id' => $id]);

            // Increment the views and score
            $story->increment('views');
            $story->increment('score');

            // Set the session flag
            Session::put($sessionKey, true);

            // Log the successful increment
            Log::channel('stories')->info('Views and score updated successfully', ['story_id' => $id, 'new_views' => $story->views, 'new_score' => $story->score]);
        } else {
            // Log that the story has already been viewed this session
            Log::channel('stories')->info('Story already viewed this session', ['story_id' => $id]);
        }

        $story->content = Purify::clean($story->content);
        //$story->id = (int) $story->id; // Ensure the ID is an integer for consistency

        log::channel('stories')->info('Story HTML purified', ['story_id' => $story->id]);

        return view('read')->with('story', $story);
    }

    /**
     * Takes form data validates or redirects it and then saves the story to the db
     * @param \Illuminate\Http\Request $request
     */
    public function submitStory(SubmitStoryRequest $request)
    {
        log::channel('stories')->info('Story content submitted', [
            'user_id' => Auth::id(),
            'title' => $request->input('title'),
            'synopsis' => $request->input('synopsis'),
            'genre' => $request->input('genre'),
        ]);

        // Get the currently authenticated user
        $user = Auth::user();

        // Create a new Story instance
        $story = new Story();
        $story->title = $request->input('title');
        $story->synopsis = $request->input('synopsis');
        $story->genre = $request->input('genre');
        //$story->content = $request->input('story');
        $story->content = Purify::clean($request->input('story'));
        $story->user_id = $user->id; // Assign the current user's ID
        $story->save();

        log::channel('stories')->info('Story saved to database', ['story_id' => $story->id, 'user_id' => $user->id]);

        //Has access to the story id if things are right
        event(new StoryPosted($story));

        log::channel('stories')->info('Story posted event dispatched', ['story_id' => $story->id, 'user_id' => $user->id]);

        // Redirect to a success page or display a success message
        return redirect()->route('index')->with('success', 'Story submitted successfully!');
    }

    /**
     * Delete one of your stories
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteStory(DeleteRequest $request, Story $story)
    {
        log::channel('stories')->info('User requested story deletion', ['story_id' => $story->id, 'user_id' => Auth::id()]);

        //Force delete removes the story from the database completly instead of soft deleting it
        $story->forceDelete();

        log::channel('stories')->info('Story deleted from database', ['story_id' => $story->id, 'user_id' => Auth::id()]);

        return back();
    }

}
