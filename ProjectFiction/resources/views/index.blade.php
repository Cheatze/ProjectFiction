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

        @foreach (\App\Enums\Genre::cases() as $index => $genre)
            <div class="col-sm">
                <a href="{{ route('stories.genre', ['genre' => $genre->value]) }}">{{ $genre->value }}</a>
            </div>

            @if (($index + 1) === 7) {{-- Check if it's the 7th iteration (index starts at 0) --}}
                </div>
                <div class="row">
            @endif
        @endforeach
        </div>
</div>

<x-Footer />
