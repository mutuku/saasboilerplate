@component('mail::message')
# Password Reset Instructions

Hello {{ $user->name }},

You have been successfully created as a new user. To set your password, please click the link below:

@component('mail::button', ['url' => url('password/reset/' . $token)])
Set Your Password
@endcomponent

If you did not request this, please ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
