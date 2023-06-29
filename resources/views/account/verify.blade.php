@extends('layouts.default')

@section('pageTitle', 'Verify a new email or telephone number')

@section('content')
    <ptu-page-header headline="Verify {{$endpoint}}"
                     topic="Cancel"
                     topic-href="{{route('account.contact')}}"></ptu-page-header>

    @include('blocks.error-block')

    <ptu-section>
        <form action="{{route('account.verify')}}" method="post">
            @csrf
            <input type="text" hidden name="endpoint" value="{{ $endpoint }}">
            <input type="text" hidden name="via" value="{{ $via }}">
            <ptu-fieldset label="Verification code">
                <section class="prose">
                    @if($via == 'call')
                        <p>Please wait, in a few moments you will receive a call which will contain a numeric code.</p>
                        <p>When the call comes, please enter the code in the box below.</p>
                    @endif
                    @if($via == 'sms')
                        <p>
                            You'll shortly receive an SMS message containing a one time code.
                            Please enter it in the box below.
                        </p>
                    @endif
                </section>
                <ptu-text-input name="code"
                                label="Verification code"
                                type="text"
                                inputmode="number"
                                width="8"
                                show-label="false"></ptu-text-input>
            </ptu-fieldset>

            <footer>
                <button class="primary-button">Confirm</button>
            </footer>
        </form>
    </ptu-section>
@endsection
