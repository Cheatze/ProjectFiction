<x-Header />
<x-navbar />

<div class="px-5">
    {{-- <a href=""></a> --}}
    <h4><a href="{{ route('profile.show', ['user' => $story->user->id]) }}">{{ $story->user->name }}</a></h4>
<h1>{{ $story->title }}</h1>

<p>{{!! $story->content !!}}</p>
</div>


<x-Footer />
