@component('mail::message')
# Password Reset

This is your password reset token: {{$token}}

If you did not request a password reset, no further action is required.

Thanks,<br>
{{ config('app.name') }}
@endcomponent