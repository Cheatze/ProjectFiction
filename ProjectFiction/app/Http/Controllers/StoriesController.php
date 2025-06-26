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

class StoriesController extends Controller
{
    public function showWrite()
    {
        return view('write');
    }

    //paginate,orderBy,select



    /**
     * 
     * Shows a paginated list of stories from new to old
     * @return \Illuminate\Contracts\View\View
     */
    public function showNew()
    {
        $list = Story::withAuthor()->paginate(15);

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

        return view('browse')->with('stories', $list);
    }

    public function showSearch(SearchRequest $request)
    {
        //$validator = $request->validated();
        $searchTerm = $request->input('search');

        // $request->validate([
        //     'search' => 'required|string|min:1', // Require at least 1 character
        // ]);

        $list = Story::withAuthor() // Start a new query builder instance for the Story model
            ->where('title', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('synopsis', 'LIKE', '%' . $searchTerm . '%')
            ->paginate(15);

        return view('browse')->with('stories', $list);
    }

    /**
     * Returns a view with the content of a certain story
     * @param mixed $id
     * @return \Illuminate\Contracts\View\View
     */
    public function showStory($id)
    {
        //Could possibly be replaced with type casting but I don't yet see how
        $story = Story::where('id', $id)->first();

        return view('read')->with('story', $story);
    }

    /**
     * Takes form data validates or redirects it and then saves the story to the db
     * @param \Illuminate\Http\Request $request
     */
    public function submitStory(SubmitStoryRequest $request)
    {
        $validator = $request->validated();

        // if ($validator->fails()) {
        //     return back()
        //         ->withErrors($validator)
        //         ->withInput(); // Return with errors and old input
        // }

        // Get the currently authenticated user
        $user = Auth::user();

        // Create a new Story instance
        $story = new Story();
        $story->title = $request->input('title');
        $story->synopsis = $request->input('synopsis');
        $story->genre = $request->input('genre');
        $story->content = $request->input('story');
        $story->user_id = $user->id; // Assign the current user's ID
        $story->save();

        //Has access to the story id if things are right
        event(new StoryPosted($story));

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
        // dd($request->user());
        // $request->user()->can('delete', $story);
        //dd($story);
        $story->delete();

        return back();
    }

}
