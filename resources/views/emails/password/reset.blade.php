<x-mail::message>
# Password Reset Request

Hello {{ $user->full_name ?? $user->email }},

You are receiving this email because we received a password reset request for your account.

<x-mail::button :url="$resetUrl">
Reset Password
</x-mail::button>

**Or use this link:** {{ $resetUrl }}

This password reset link will expire in {{ $expirationMinutes }} minutes.

If you did not request a password reset, no further action is required.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
