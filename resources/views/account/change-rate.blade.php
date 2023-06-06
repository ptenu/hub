@extends('layouts.default')

@section('pageTitle', 'Update subscription amount')

@section('content')
    <ptu-page-header headline="Update how much you pay" topic="Back to membership details" topic-href="/account/membership"></ptu-page-header>

    @include('blocks.error-block')

    <ptu-section>
        <form action="/account/membership/change-rate" method="post">
            {{ csrf_field() }}
            <ptu-field label="How much would you like to pay?">
                <p style="margin-bottom: var(--layout-gap); color: var(--colour-grey-700)">
                    We suggest one hours wage per month, but whatever you can afford will help run the organisation.
                </p>
                <ptu-amount-input id="amount" name="amount"></ptu-amount-input>
            </ptu-field>
            <footer>
                <p style="font-size: var(--fs-small); margin-bottom: 1em;">
                    You won't be charged now, we'll use the new amount from your next payment day.
                </p>
                <button>Save</button>
            </footer>
        </form>
    </ptu-section>
@endsection
