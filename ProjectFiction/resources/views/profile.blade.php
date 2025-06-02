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
                    <form action="" method="POST">
                        @csrf
                        <button>Delete</button>
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
