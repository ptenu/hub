<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
{{ config('app.name') }} @if(config('app.env') != 'production')[ {{ config('app.env') }} ]@endif
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{{ $subcopy }}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
@if(config('app.env') != 'production')
    [ {{ config('app.env') }} ]
@endif

**{{ __('legal.company_name') }}**

{{ __('legal.company_registration') }}.
{{ __('legal.company_address') }}

</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
