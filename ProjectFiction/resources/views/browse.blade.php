<x-Header />
<x-navbar />

<h1>Browing page</h1>

<div>
<ul>
    @foreach ($stories as $story)
        <div>
        <li><a href="{{ route('stories.read', ['id' => $story->id]) }}">{{ $story->title }}</a></li>
        <li><a href="{{ route('profile.show', ['id' => $story->user->id]) }}">{{ $story->user->name }}</a></li>
        <li>{{ $story->genre }}</li>
        <li><p>{{ $story->synopsis }}</p></li>
        </div>
    @endforeach
</ul>
</div>
{{ $stories->links() }}

<x-Footer />