<form action="{{ route('login') }}" method="POST">
    @csrf
    <h2>Login to your account</h2>
    <label for="email">Email:</label>
    <input type="email" name="email" value="{{ old('email') }}" required>
    <label for="password">Password:</label>
    <input type="password" name="password" required>
    <button type="submit" class="btn btn-primary">Login</button>
    <!-- validation errors -->
    @if ($errors->any())
        <ul class="">
            @foreach ($errors->all() as $error)
                <li class="">{{ $error }}</li>
            @endforeach
        </ul>
    @endif
</form>
