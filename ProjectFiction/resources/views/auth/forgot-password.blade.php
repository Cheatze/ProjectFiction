<x-Header />
<x-navbar />

<h3>Password Forgotten</h3>

<form action="" method="POST">
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control w-auto" name="email" value="{{ old('email') }}" required>
    </div>
    <button type="submit" class="btn btn-primary">Send reset email</button>


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
