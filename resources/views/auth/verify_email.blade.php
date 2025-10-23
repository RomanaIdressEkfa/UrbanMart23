<h1>ইমেইল ভেরিফিকেশন</h1>
<form method="POST" action="{{ route('verify-code') }}">
    @csrf
    <input type="text" name="code" placeholder="Enter your verification code">
    <button type="submit">Verify</button>
</form>

<form method="POST" action="{{ route('send-verification-code') }}">
    @csrf
    <input type="hidden" name="email" value="{{ auth()->user()->email }}">
    <button type="submit">Resend Code</button>
</form>

