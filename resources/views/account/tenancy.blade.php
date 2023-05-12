@extends('layouts.default')

@section('pageTitle', 'Your tenancy details')

@section('content')

    <ptu-page-header headline="Your tenancy"></ptu-page-header>
    <ptu-tabs>
        <ptu-tab href='/account'>Details</ptu-tab>
        <ptu-tab href='/account/tenancy' selected>Tenancies</ptu-tab>
        <ptu-tab href='/account/membership'>Membership</ptu-tab>
        <ptu-tab href='/account/contact-preferences'>Contact Preferences</ptu-tab>
    </ptu-tabs>

    <ptu-section class="prose top-padding">
        <p>This page contains the information we have about your tenancy.</p>

        @if(!$user->residentialInterest)
            <article class="card">
                <header>
                    There is no address on file for you.
                </header>
                <p>Before you can add any information about your tenancy, you'll need to add an address to your account.</p>
                <footer>
                    <a href="/account/update-address">Update your address</a>
                </footer>
            </article>
        @endif

        <dl class="bottom-padding">
            @if($user->residentialInterest)
                <dt>Current residential address</dt>
                <dd style="white-space: pre">{{$user->residentialInterest->address->getMultiLineAddress()}}</dd>
                <dd><a href="/account/update-address">Change address</a></dd>
            @endif
        </dl>

        @if(!$user->currentTenancy)
            <article class="card">
                <header>
                    There is no tenancy information on file for you.
                </header>
                <p>
                    It's a good idea to add this information now, as it will speed things up if we need to provide
                    support to you in the future.
                </p>
                <footer>
                    <a href="/account/update-tenancy">Add tenancy information</a>
                </footer>
            </article>
        @endif

    </ptu-section>
@endsection
