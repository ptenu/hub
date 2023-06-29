@extends('layouts.default')

@section('pageTitle', 'Add new telephone number')

@section('content')
    <ptu-page-header headline="Add a new telephone number"
                     topic="Back to contact preferences"
                     topic-href="{{route('account.contact')}}"></ptu-page-header>

    @include('blocks.error-block')

    <ptu-section>
        <form method="post">
            {{ csrf_field() }}
            <ptu-fieldset label="New telephone number">
                <ptu-text-input name="number"
                                label="Telephone number"
                                type="text"
                                inputmode="tel"
                                width="15"
                                show-label="false"></ptu-text-input>
            </ptu-fieldset>

            <ptu-fieldset label="Preferences">
                <section class="prose">
                    <p>
                        What can we use this phone number for?
                    </p>
                </section>
                <ptu-checkbox name="consent[]" value="M">Reminders of events and meetings</ptu-checkbox>
                <ptu-checkbox name="consent[]" value="R">Requests for help if a member needs urgent support</ptu-checkbox>
                <ptu-checkbox name="consent[]" value="C">Updates on casework you're involved with</ptu-checkbox>
            </ptu-fieldset>

            <footer>
                <section class="prose">
                    <p>When you click save, we'll send you an SMS or call you with a one-time code, and you'll need to enter on the next screen.</p>
                </section>
                <button class="primary-button">Save</button>
            </footer>
        </form>
    </ptu-section>
@endsection
