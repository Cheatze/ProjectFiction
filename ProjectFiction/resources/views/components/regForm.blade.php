<div class="container-fluid ps-4" style="max-width: 500px;">
    <form action="{{ route('register') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <h2 class="mb-4">Create an account</h2>

        <div class="mb-3">
            <label for="name" class="form-label">Username</label>
            <input type="text" class="form-control w-auto" name="name" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control w-auto" name="email" value="{{ old('email') }}" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control w-auto" name="password" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm password</label>
            <input type="password" class="form-control w-auto" name="password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-primary">Register</button>

        <!--Validation errors-->
        @if ($errors->any())
            <ul class="">
                @foreach ($errors->all() as $error)
                    <li class="">{{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </form>
</div>
