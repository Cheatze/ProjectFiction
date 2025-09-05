<x-Header />
<x-navbar />
<h1>Public profile page for {{ $user->name }}</h1>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<hr>
@auth
    @if (Auth::id() != $user->id && $isSubscribed == false)
        <div>
            <form action="{{ route('subscribe') }}" method="POST">
                @csrf
                <input type="hidden" value="{{ $user->id }}" name="id">
                <button>Subscribe to {{ $user->name }}</button>
            </form>
        </div>
        <br>
    @elseif (Auth::id() != $user->id)
        <form action="{{ route('unsubscribe') }}" method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" value="{{ $user->id }}" name="id">
            <button type="submit">Unsubscribe</button>
        </form>
    @endif
@endauth

    <!--Validation errors-->
    {{-- @if ($error->any())
        <ul class="">
            @foreach ($error->all() as $error)
                <li class="">{{ $error }}</li>
            @endforeach
         </ul>
    @endif --}}

<div>
    <h3>Stories</h3>
<ul>
    @foreach ($stories as $story)
        <div>

            <li><a href="{{ route('stories.read', ['story' => $story->id]) }}">{{ $story->title }}</a></li>
            <li>{{ $story->genre }}</li>
            <li>
                <p>{{ $story->synopsis }}</p>
            </li>
            <hr>
        </div>
    @endforeach
</ul>
</div>
{{ $stories->links() }}

<x-Footer />
