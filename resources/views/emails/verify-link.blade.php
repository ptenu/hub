<x-mail::message>
### Please verify this email address
This email address has been added to your PTU account, to verify that it belongs to you
please click the link below.

It will open a browser window and confirm the verification.

*Do not share this link with anyone.*

<a href="{{$url}}">
    {{$url}}
</a>

If you have problems clicking the link, copying and pasting the link into your browser address bar.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
