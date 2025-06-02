<x-Header />
<x-navbar />
<h1>Profile page for {{ $user->name }}</h1>

<h3>Stories</h3>

<div>
<ul>
    @foreach ($stories as $story)
        <div>

            <li><a href="{{ route('stories.read', ['id' => $story->id]) }}">{{ $story->title }}</a></li>
            <li>{{ $story->genre }}</li>
            <li>
                <p>{{ $story->synopsis }}</p>
            </li>
            @if (Auth::id() == $user->id)
                <li>
                    <form action="{{ route('delete') }}" method="POST" onsubmit="return confirm('Do you really want to delete this story?')">
                        @csrf
                        <input type="hidden" value="{{ $story->id }}">
                        <button>Delete {{ $story->title }}</button>
                    </form>
                </li>
            @endif
            <br>
        </div>
    @endforeach
</ul>
</div>
{{ $stories->links() }}

<x-Footer />
