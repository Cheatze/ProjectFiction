<form action="" method="POST">
    @csrf
    <h2>Create an account</h2>

    <label for="username">Username:</label>
    <input type="text" name="name" value="{{ old('name') }}" required>

    <label for="email">Email:</label>
    <input type="email" name="email" value="{{ old('email') }}" required>

    <label for="password">Password:</label>
    <input type="password" name="password" required>

    <button type="submit" class="btn btn-primary">Register</button>
</form>
