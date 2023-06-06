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
            <section>
                <hgroup>
                    <h3>Messages</h3>
                    <p>All emails recently sent to you through our system will appear here.</p>
                </hgroup>
                <div class="card">
                    There are no messages to show.
                </div>
            </section>

            <section>
                <hgroup>
                    <h3>Upcoming events</h3>
                    <p>Events and meetings happening in the near future will be shown here.</p>
                </hgroup>
                <div class="card">
                    There are no events to show.
                </div>
            </section>

            <section>
                <hgroup>
                    <h3>Cases</h3>
                    <p>This list will show any support cases you are currently involved in.</p>
                </hgroup>
                <div class="card">
                    You have no open cases.
                    <footer>
                        <a href="/">Request help</a>
                    </footer>
                </div>
            </section>
        </article>

        <nav slot="sidebar">
            <h6 style="font-size: var(--fs-h4)">Useful links</h6>
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
