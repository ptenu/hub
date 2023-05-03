<x-mail::message>
# This is your one-time password.
You can use this to log into the Peterborough Tenants' Union website.

*Do not share this password with anyone*. We will never ask you to tell us the password contained in this email.

<x-mail::panel>
**{{ $password }}**
</x-mail::panel>

This password will expire in a few minutes. You will not be able to request another password until then.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
