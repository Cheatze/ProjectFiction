<x-Header />
<x-navbar />

<div>
    {{-- <a href=""></a> --}}
    <h4>{{ $story->user->name }}</h4>
<h1>{{ $story->title }}</h1>

<p>{{ $story->content }}</p>
</div>


<x-Footer />
