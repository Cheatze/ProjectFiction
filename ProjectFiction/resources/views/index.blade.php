<x-Header />
<x-navbar />
@if (\Session::has('message'))
    <h1>{!! \Session::get('message') !!}</h1>
    <hr>
@endif

<h1>Index page</h1>

<div><h3>Newest by genre</h3></div>

<div class="container">
    <div class="row">
        <div class="col-sm">
            <a href="{{ route('stories.genre', ['genre' => 'Action']) }}">Action</a>
        </div>
        <div class="col-sm">
            <a href="{{ route('stories.genre', ['genre' => 'Essay']) }}">Essay</a>
        </div>
        <div class="col-sm">
            <a href="{{ route('stories.genre', ['genre' => 'Fiction']) }}">Fiction</a>
        </div>
        <div class="col-sm">
            <a href="{{ route('stories.genre', ['genre' => 'Fantasy']) }}">Fantasy</a>
        </div>
        <div class="col-sm">
            <a href="{{ route('stories.genre', ['genre' => 'Mystery']) }}">Mystery</a>
        </div>
        <div class="col-sm">
            <a href="{{ route('stories.genre', ['genre' => 'Science Fiction']) }}">Science Fiction</a>
        </div>
        <div class="col-sm">
            <a href="{{ route('stories.genre', ['genre' => 'Horror']) }}">Horror</a>
        </div>
    </div>
        <div class="row">
            <div class="col-sm">
                <a href="{{ route('stories.genre', ['genre' => 'Historical']) }}">Historical</a>
            </div>
            <div class="col-sm">
                <a href="{{ route('stories.genre', ['genre' => 'Humor']) }}">Humor</a>
            </div>
            <div class="col-sm">
                <a href="{{ route('stories.genre', ['genre' => 'Thriller']) }}">Thriller</a>
            </div>
            <div class="col-sm">
                <a href="{{ route('stories.genre', ['genre' => 'Mythology']) }}">Mythology</a>
            </div>
            <div class="col-sm">
                <a href="{{ route('stories.genre', ['genre' => 'Romance']) }}">Romance</a>
            </div>
            <div class="col-sm">
                <a href="{{ route('stories.genre', ['genre' => 'Biography']) }}">Biography</a>
            </div>
            <div class="col-sm">
                <a href="{{ route('stories.genre', ['genre' => 'Supernatural']) }}">Supernatural</a>
            </div>
        </div>
</div>

<x-Footer />
