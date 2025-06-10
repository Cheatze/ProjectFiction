<x-Header />
<x-navbar />
<h1>Profile page for {{ $user->name }}</h1>

<hr>
@if (Auth::id() != $user->id && $isSubscribed == false)
    <div>
        <form action="{{ route('subscribe') }}" method="POST">
            @csrf
            <input type="hidden" value="{{ $user->id }}" name="id">
            <button>Subscribe to {{ $user->name }}</button>
        </form>
    </div>
    <br>
@else

    <form action="{{ route('unsubscribe') }}" method="POST">
        @csrf
        @method('DELETE')
        <input type="hidden" value="{{ $user->id }}" name="id">
        <button type="submit">Unsubscribe</button>

@endif

    <!--Validation errors-->
    @if ($error->any())
        <ul class="">
            @foreach ($error->all() as $error)
                <li class="">{{ $error }}</li>
            @endforeach
         </ul>
    @endif

<div>
    <h3>Stories</h3>
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
                        <input type="hidden" value="{{ $story->id }}" name="id">
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
