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
        <div class="col-sm">
            <a href="">Action</a>
        </div>
        <div class="col-sm">
            <a href="">Essay</a>
        </div>
        <div class="col-sm">
            <a href="">Fiction</a>
        </div>
        <div class="col-sm">
            <a href="">Fantasy</a>
        </div>
        <div class="col-sm">
            <a href="">Mystery</a>
        </div>
        <div class="col-sm">
            <a href="">Science Fiction</a>
        </div>
        <div class="col-sm">
            <a href="">Horror</a>
        </div>
    </div>
        <div class="row">
            <div class="col-sm">
                <a href="">Historical</a>
            </div>
            <div class="col-sm">
                <a href="">Humor</a>
            </div>
            <div class="col-sm">
                <a href="">Thriller</a>
            </div>
            <div class="col-sm">
                <a href="">Mythology</a>
            </div>
            <div class="col-sm">
                <a href="">Romance</a>
            </div>
            <div class="col-sm">
                <a href="">Biography</a>
            </div>
            <div class="col-sm">
                <a href="">Supernatural</a>
            </div>
        </div>
</div>

<x-Footer />
