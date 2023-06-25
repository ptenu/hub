@extends('layouts.default')

@section('pageTitle', 'Member dashboard')

@section('content')
    <ptu-hero>
        <h1 class='title' style="font-size: var(--fs-big)">
            Welcome back {{ $user->given_name }}
        </h1>
    </ptu-hero>

    <ptu-section class="top-padding" sidebar="right">
        <article style="display: flex; flex-direction: column; gap: calc(var(--layout-gap) * 2)">
            <ptu-box heading="Messages">
                There are no messages to show.
            </ptu-box>

            <ptu-box heading="Upcoming events">
                There are no events to show.
            </ptu-box>

            <ptu-box heading="Your cases">
                <a href="/" slot="actions">Request help</a>
                You have no open cases.
            </ptu-box>
        </article>

        <nav slot="sidebar">
            <ul class="list">
                <li>
                    <a href="/account">View and update your personal details</a>
                </li>
                <li>
                    <a href="/account/update-address">Update your address</a>
                </li>
                <li>
                    <a href="/account/tenancy">View and update information about your tenancy</a>
                </li>
            </ul>
        </nav>
    </ptu-section>
@endsection
