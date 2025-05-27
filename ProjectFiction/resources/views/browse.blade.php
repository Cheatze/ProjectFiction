<x-Header />
<x-navbar />

<h1>Browing page</h1>


<ul>
    @foreach ($stories as $story)
        <div>
        {{-- <li>{{ $story->id }}</li>
        <li>{{ $story->title }}</li> --}}
        <li><a href="{{ route('stories.read', ['id' => $story->id]) }}">{{ $story->title }}</a></li>
        <li>{{ $story->user->name }}</li>
        <li>{{ $story->genre }}</li>
        <li><p>{{ $story->synopsis }}</p></li>
        </div>
    @endforeach
</ul>
{{ $stories->links() }}

<x-Footer />