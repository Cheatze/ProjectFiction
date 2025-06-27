<x-Header />
<x-navbar />
<h1>Private profile page for {{ $user->name }}</h1>
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

                <li><a href="{{ route('stories.read', ['id' => $story->id]) }}">{{ $story->title }}</a></li>
                <li>{{ $story->genre }}</li>
                <li>
                    <p>{{ $story->synopsis }}</p>
                </li>
                @if (Auth::id() == $user->id)
                    <li>
                        <form action="{{ route('delete', ['story' => $story->id]) }}" method="POST"
                            onsubmit="return confirm('Do you really want to delete this story?')">
                            @csrf
                            {{-- <input type="hidden" value="{{ $story->id }}" name="story"> --}}
                            <button>Delete {{ $story->title }}</button>
                        </form>
                    </li>
                @endif
                <hr>
            </div>
        @endforeach
    </ul>
</div>
{{ $stories->links() }}

<x-Footer />