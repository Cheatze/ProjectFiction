<x-Header />
<x-navbar />

<div class="px-5">
    {{-- <a href=""></a> --}}
    <h4><a href="{{ route('profile.show', ['user' => $story->user->id]) }}">{{ $story->user->name }}</a></h4>
<h1>{{ $story->title }}</h1>
<!--HTML input is Santized in Laravel 12 by default right?
    I've tried adding script tags to the input text field and they do not show up in the database story content.
    I could do more about it if I should-->
<p>{!! $story->content !!}</p>
</div>

@auth
    <div class="px-5">
        <form action="{{ route('stories.like', ['story' => $story->id]) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">Like this story</button>
        </form>
    </div>
@endauth

<x-Footer />
