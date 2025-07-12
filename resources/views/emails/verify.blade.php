<p>Hi {{ $name ?? 'User' }},</p>
<p>Please click the link below to verify your email address:</p>
<p>
    <a href="{{ config('app.url') }}/api/verification/email/verify/{{ $token }}">
        Verify Email
    </a>
</p>
<p>If you did not request this, please ignore this email.</p> 
