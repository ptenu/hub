@extends('layouts.default')

@section('pageTitle', 'Update payment day')

@section('content')
    <ptu-page-header headline="Update when you pay each month" topic="Back to membership details" topic-href="/account/membership"></ptu-page-header>

    @include('blocks.error-block')

    <ptu-section>
        <form action="/account/membership/change-payment-day" method="post">
            {{ csrf_field() }}
            <ptu-field label="Which day of the month would you like us to take the payment?"
                       control-id="day"
            >
                <input id="day"
                       name="day"
                       value="{{ $user->membership->payment_day }}"
                       style="max-width: 6ch"
                >
            </ptu-field>
            <footer>
                <p style="font-size: var(--fs-small); margin-bottom: 1em;">
                    You won't be charged now, we'll use the new date for your next payment.
                </p>
                <button>Save</button>
            </footer>
        </form>
    </ptu-section>
@endsection
