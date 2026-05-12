@component('mail::message')
# Hello {{ $guide->name }},

We received a request to reset your password for the **{{ config('app.name') }}**. 
If this was you, please click the button below to verify your identity.

@component('mail::button', ['url' => $url])
Yes, it is me
@endcomponent

Once verified, the system will automatically generate a new secure password and send it to you via email.

If you did not request this, please ignore this email and your current password will remain unchanged.

Thanks,<br>
{{ config('app.name') }}
@endcomponent