@extends('layouts.default')

@section('pageTitle', 'Change your address')

@section('content')
    <ptu-page-header headline="Update your address"></ptu-page-header>
    <ptu-section class="prose">
        <p>
            You can use this page to update the address we have on file for you.
        </p>
        <p>
            This address will be used both as your home address <b>and</b> as the address for your payment method.
            For this reason, please make sure you use the same address as registered with your bank.
        </p>
    </ptu-section>
    <ptu-section>
        <form method="get">
            <ptu-fieldset label="Postcode" inline>
                <ptu-text-input name="postcode[]"
                                width="4"
                                label="PE1 - PE9"
                                @if(isset($postcode))
                                    initial-value="{{ $postcode[0] }}"
                                @endif></ptu-text-input>
                <ptu-text-input name="postcode[]"
                                width="4"
                                label="e.g. 1HQ"
                                @if(isset($postcode))
                                    initial-value="{{ $postcode[1] }}"
                                @endif></ptu-text-input>
                <button style="align-self: start">Search</button>
            </ptu-fieldset>
        </form>

        @if(isset($addresses))
            @if(count($addresses) < 1)
                <p class="card">No addresses found for that postcode.</p>
            @else
                <form id="address" method="post">
                    {{ csrf_field() }}
                    <ptu-field label="Select address" control-id="new-address">
                        <select id="new-address" name="uprn">
                            @foreach($addresses as $address)
                                <option value="{{$address->uprn}}">
                                    @if($address->tao)
                                        {{$address->tao}},
                                    @endif
                                    @if($address->sao)
                                        {{$address->sao}},
                                    @endif
                                    {{$address->pao}},
                                    @if($address->locality){{$address->locality}}, @endif
                                    @if($address->town_name){{$address->town_name}}@endif
                                </option>
                            @endforeach
                        </select>
                    </ptu-field>
                </form>
            @endif
        @endif

        <footer class="top-padding">
            @if(isset($addresses))
                <button form="address">Save</button>
            @endif
            <a href="/account">Cancel</a>
        </footer>
    </ptu-section>
@endsection
