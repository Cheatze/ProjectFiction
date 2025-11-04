<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Events\StoryPosted;
use App\Events\StoryViewed;
use App\Enums\Genre;
use Illuminate\Validation\Rules\Enum;
use App\Http\Requests\SubmitStoryRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\DeleteRequest;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Services\StoryService;

class StoriesController extends Controller
{
    // protected $storyService;
    // public function __construct(StoryService $storyService)
    // {
    //     $this->storyService = $storyService;
    // }

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
    public function showNew(StoryService $storyService)
    {
        //$list = Story::withAuthor()->paginate(15);
        $list = $storyService->getNewStories();

        Log::channel('stories')->info('Showing new stories', ['count' => $list->count()]);

        return view('browse')->with('stories', $list);
    }

    /**
     * 
     * Shows a paginated list of stories ordered by score
     * @return \Illuminate\Contracts\View\View
     */
    public function showPopular(StoryService $storyService)
    {
        //$list = Story::Popular()->paginate(15);
        $list = $storyService->getPopularStories();

        Log::channel('stories')->info('Showing popular stories', ['count' => $list->count()]);

        return view('browse')->with('stories', $list);
    }

    /**
     * 
     * Returns a paginated view of stories with a certain genre using an enum
     * @param \app\Enums\Genre $genre
     * @return \Illuminate\Contracts\View\View
     */
    public function showGenre(Genre $genre, StoryService $storyService)
    {
        $theGenre = $genre->value;

        // $list = Story::withAuthor()
        //     ->where('genre', $theGenre)
        //     ->paginate(15);

        $list = $storyService->getStoriesByGenre($genre);

        log::channel('stories')->info('Showing stories by genre', ['genre' => $theGenre, 'count' => $list->count()]);

        return view('browse')->with('stories', $list);
    }

    public function showSearch(SearchRequest $request, StoryService $storyService)
    {
        //$validator = $request->validated();
        $searchTerm = $request->input('search');

        log::channel('stories')->info('User searched for stories', ['search_term' => $searchTerm]);

        // $list = Story::withAuthor() // Start a new query builder instance for the Story model
        //     ->where('title', 'LIKE', '%' . $searchTerm . '%')
        //     ->orWhere('synopsis', 'LIKE', '%' . $searchTerm . '%')
        //     ->paginate(15);

        $list = $storyService->searchStories($searchTerm);

        log::channel('stories')->info('Search results count', ['count' => $list->count()]);

        return view('browse')->with('stories', $list);
    }

    /**
     * Returns a view with the content of a certain story
     * @param mixed $id
     * @return \Illuminate\Contracts\View\View
     */
    public function showStory(Story $story, StoryService $storyService)
    {
        log::channel('stories')->info('User accessed story reading page', ['story_id' => $story->id]);

        //Could possibly be replaced with type casting but I get errors when I try
        //$story = Story::where('id', $id)->first();

        if (!$story) {
            log::channel('stories')->error('Story not found', ['story_id' => $story->id]);
            abort(404); // Or handle the error as needed
        }

        log::channel('stories')->info('Story content retrieved', ['story_id' => $story->id]);

        //event(new StoryViewed($story));

        $story = $storyService->getStory($story);

        // Check if a user is authenticated
        $currentUser = Auth::user();

        $hasLiked = $storyService->hasUserLikedStory($story, $currentUser);

        //$story->content = Purify::clean($story->content);

        log::channel('stories')->info('Story HTML purified', ['story_id' => $story->id]);

        return view('read')->with('story', $story)->with('hasLiked', $hasLiked);
    }

    /**
     * Takes form data validates or redirects it and then saves the story to the db
     * @param \Illuminate\Http\Request $request
     */
    public function submitStory(SubmitStoryRequest $request, StoryService $storyService)
    {
        log::channel('stories')->info('Story content submitted', [
            'user_id' => Auth::id(),
            'title' => $request->input('title'),
            'synopsis' => $request->input('synopsis'),
            'genre' => $request->input('genre'),
        ]);

        // Get the currently authenticated user
        $user = Auth::user();

        $data = $request->validated();
        // Use the StoryService to create and save the story
        $story = $storyService->createStory($data, $user);

        log::channel('stories')->info('Story saved to database', ['story_id' => $story->id, 'user_id' => $user->id]);

        //Has access to the story id if things are right
        //event(new StoryPosted($story));

        log::channel('stories')->info('Story posted event dispatched', ['story_id' => $story->id, 'user_id' => $user->id]);

        // Redirect to a success page or display a success message
        return redirect()->route('index')->with('success', 'Story submitted successfully!');
    }

    /**
     * Delete one of your stories
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteStory(DeleteRequest $request, Story $story, StoryService $storyService)
    {
        log::channel('stories')->info('User requested story deletion', ['story_id' => $story->id, 'user_id' => Auth::id()]);

        //Force delete removes the story from the database completly instead of soft deleting it
        $storyService->deleteStory($story);
        //$story->forceDelete();

        log::channel('stories')->info('Story deleted from database', ['story_id' => $story->id, 'user_id' => Auth::id()]);

        return back();
    }

    /**
     * Display a random story.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function random(StoryService $storyService)
    {
        // Fetch a random story from the database.
        $story = $storyService->getRandomStory();
        //$story = Story::inRandomOrder()->first();

        log::channel('stories')->info('Random story requested', ['story_id' => $story ? $story->id : null]);

        // Check if a story was found.
        if ($story) {
            // Redirect to the show method with the random story's ID.
            return redirect()->route('stories.read', ['story' => $story->id]);
        }

        log::channel('stories')->warning('No stories found for random selection');

        // If no story is found, you can return a 404 or a custom view.
        abort(404, 'No stories found.');
    }

}
