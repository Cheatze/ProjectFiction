<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Events\StoryPosted;

class StoriesController extends Controller
{
    public function showWrite()
    {
        return view('write');
    }

    /**
     * 
     * Shows a paginated list of stories from new to old
     * @return \Illuminate\Contracts\View\View
     */
    public function showNew()
    {
        $list = Story::with('user') // This is the key!
            ->select('id', 'title', 'genre', 'synopsis', 'user_id')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('browse')->with('stories', $list);
    }

    public function showGenre($genre)
    {
        $list = Story::with('user') // This is the key!
            ->select('id', 'title', 'genre', 'synopsis', 'user_id')
            ->orderBy('id', 'desc')
            ->where('genre', $genre)
            ->paginate(15);

        return view('browse')->with('stories', $list);
    }

    public function showSearch($search)
    {
        $searchTerm = $search->input('search');

        $list = Story::with('user') // Start a new query builder instance for the Story model
            ->where('title', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('synopsis', 'LIKE', '%' . $searchTerm . '%')
            ->select('id', 'title', 'genre', 'synopsis', 'user_id')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('browse')->with('stories', $list);
    }

    public function showStory($id)
    {
        $story = Story::with('user')->where('id', $id)->first();

        return view('read')->with('story', $story);
    }

    /**
     * Takes form data validates or redirects it and then saves the story to the db
     * @param \Illuminate\Http\Request $request
     */
    public function submitStory(Request $request)
    {
        // Define the genres (must match your form's options exactly!)
        $allowedGenres = [
            'Action',
            'Essay',
            'Fiction',
            'Fantasy',
            'Mystery',
            'Science Fiction',
            'Horror',
            'Historical',
            'Humor',
            'Thriller',
            'Mythology',
            'romance',
            'Biography',
            'Supernatural'
        ];

        // Validate the incoming data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'synopsis' => 'required|string|max:500|min:25',
            'genre' => ['required', 'string', 'in:' . implode(',', $allowedGenres)],
            'story' => 'required|string|max:1024000|min:500', // 1MB in kilobytes (1024 * 1000)
        ], [
            'story.max' => 'The story content must not exceed 1MB.',
            'genre.in' => 'Invalid genre selected.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput(); // Return with errors and old input
        }

        // Get the currently authenticated user
        $user = Auth::user();

        // Or, more concisely:
        // $userId = Auth::id();

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
    public function deleteStory(Request $request)
    {
        $story = Story::where('user_id', Auth::id())
            ->where('id', $request->input('id'))
            ->first()
            ->delete();

        return back();
    }

}
