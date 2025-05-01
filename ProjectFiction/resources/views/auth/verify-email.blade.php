<x-Header />
<x-navbar />
<h1>Please click on the link in your verification email</h1>

<form action="{{ route('verification.send') }}" method="POST">
    @csrf
    <label for="Resend" class="form-label">Resend verification email</label>
    <button type="submit" id="Resend" class="btn btn-primary">Resend</button>
</form>

<x-Footer />