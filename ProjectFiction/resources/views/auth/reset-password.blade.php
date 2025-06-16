<x-Header />
<x-navbar />

<h3>Password Reset</h3>

<form action="{{ route('password.update') }}" method="POST">
    @csrf
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

    <input type="hidden" value="{{ $token }}" name="token">

    <button type="submit" class="btn btn-primary">Reset Password</button>


    <!--Validation errors-->
    @if ($errors->any())
        <ul class="">
            @foreach ($errors->all() as $error)
                <li class="">{{ $error }}</li>
            @endforeach
        </ul>
    @endif
</form>

<x-Footer />