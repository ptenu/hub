[{{ $slot }}]({{ $url }})

@auth
Membership number: {{ auth()->user()->id }}
@endauth
