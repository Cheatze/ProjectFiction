<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('index') }}">Fictionality</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
            aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            
                <div class="navbar-nav me-auto">
                    @auth
                    <a class="nav-link" href="{{ route("show.write") }}">Write</a>
                    @endAuth
                    <a class="nav-link" href="{{ route("show.newest") }}">Newest</a>
                    <form action="{{ route('show.search') }}" class="d-flex">
                        <input name="search" class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                </div>
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link" aria-current="page" href="{{ route('index') }}">Home</a>
                @guest
                    <a class="nav-link" href="{{ route('show.register') }}">Register</a>
                    <a class="nav-link" href="{{ route('show.login') }}">Login</a>
                @endGuest
                @auth
                    <a href="{{ route('profile.show', ['id' => Auth::id()]) }}">{{ Auth::user()->name }}</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn">Logout</button>
                    </form>
                @endAuth
            </div>
        </div>
    </div>
</nav>
