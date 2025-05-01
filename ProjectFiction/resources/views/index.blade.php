<x-Header />
<x-navbar />
@if (\Session::has('message'))
    <h1>{!! \Session::get('message') !!}</h1>
    <hr>
@endif

<h1>Index page</h1>

<x-Footer />
