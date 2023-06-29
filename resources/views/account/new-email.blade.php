@extends('layouts.default')

@section('pageTitle', 'Add a new email address')

@section('content')
    <ptu-page-header headline="Add a new email address"
                     topic="Back to contact preferences"
                     topic-href="{{route('account.contact')}}"></ptu-page-header>

    @include('blocks.error-block')

    <ptu-section>
        <form method="post">
            {{ csrf_field() }}

            <ptu-fieldset label="New email address">
                <ptu-text-input name="email"
                                label="Address"
                                type="email"
                                inputmode="email"
                                width="35"
                                show-label="false"></ptu-text-input>
            </ptu-fieldset>

            <ptu-fieldset label="Preferences">
                <section class="prose">
                    <p>
                        Which types of emails would you like us to send to this address?
                    </p>
                    <p>
                        We'll always send some kinds of emails; this includes notices of meetings and
                        important information about the Union (like elections), as well as updates
                        to any cases you are involved in.
                    </p>
                    <p>
                        We'll send things to your primary email first, and use a secondary email if
                        you have set a preference below.
                    </p>
                </section>
                <ptu-checkbox name="consent[]" value="N">General newsletters</ptu-checkbox>
                <ptu-checkbox name="consent[]" value="A">Information about our campaigns</ptu-checkbox>
                <ptu-checkbox name="consent[]" value="E">Information about upcoming events</ptu-checkbox>
                <ptu-checkbox name="consent[]" value="C">Updates on cases you're involved with</ptu-checkbox>
                <ptu-checkbox name="consent[]" value="M">New messages from other members</ptu-checkbox>
                <ptu-checkbox name="consent[]" value="E">Replies to your posts on the forum</ptu-checkbox>
            </ptu-fieldset>

            <footer>
                <section class="prose">
                    <p>
                        We won't start using this address until you verify it. Check your emails
                        for a verification link.
                    </p>
                </section>
                <button class="primary-button">Save</button>
            </footer>
        </form>
    </ptu-section>
@endsection
