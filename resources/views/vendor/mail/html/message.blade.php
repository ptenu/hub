<x-mail::layout>
    {{-- Header --}}
    <x-slot:header>
        <x-mail::header :url="config('app.url')">
            @if(config('app.env') != 'production')
                <p>[ {{ config('app.env') }} ]</p>
            @endif
            @auth
                <p>
                    @if(isset(request()->user()->membership))
                        {{ request()->user()->membership->id }}
                    @else
                        {{request()->user()->id}}
                    @endif
                </p>
            @elseauth
                <p>Peterborough Tenants Union</p>
            @endauth
        </x-mail::header>
    </x-slot:header>

    {{-- Body --}}
    <table class="body" data-made-with-foundation>
        <tr>
            <td class="float-center" align="center" valign="top">
                <center>
                    <table class="container">
                        <tr>
                            <td>
                                <table class="row">
                                    <tr>
                                        <th class="first columns">
                                            {{ Illuminate\Mail\Markdown::parse($slot) }}
                                        </th>
                                        <th class="expander"></th>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </center>
            </td>
        </tr>
    </table>

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

{{ __('legal.company_registration') }}. <br/>
{{ __('legal.company_address') }}
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
