<x-Header />
<x-navbar />
<h1>Please click on the link in your verification email</h1>

<form action="{{ route('verification.send') }}" method="POST">
    @csrf
    <label for="Resend" class="form-label">Resend verification email</label>
    <button type="submit" id="Resend" class="btn btn-primary">Resend</button>
</form>

{{-- @isset($message)
    <h1>{{ $message }}</h1>
@endisset --}}

@if (\Session::has('message'))
    <h1>{!! \Session::get('message') !!}</h1>
@endif

<x-Footer />