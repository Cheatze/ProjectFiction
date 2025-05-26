<x-Header />
<x-navbar />

<h1>Browing page</h1>


<ul>
    @foreach ($stories as $story)
        <div>
        <li>{{ $story->id }}</li>
        <li>{{ $story->title }}</li>
        <li>{{ $story->user->name }}</li>
        <li><p>{{ $story->synopsis }}</p></li>
        </div>
    @endforeach
</ul>
{{ $stories->links() }}

<x-Footer />