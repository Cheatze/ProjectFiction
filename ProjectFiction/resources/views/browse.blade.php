<x-Header />
<x-navbar />

<h1>Browing page</h1>


<ul>
    @foreach ($stories as $story)
        <li>{{ $story->title }}</li>
    @endforeach
</ul>

<x-Footer />