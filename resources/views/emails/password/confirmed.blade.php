<x-mail::message>
# Password Reset Successful

Hello {{ $user->full_name ?? $user->email }},

Your password has been successfully reset.

If you did not make this change, please contact us immediately.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
