@extends('layouts.default')

@section('pageTitle', 'Your membership')

@section('content')

    <ptu-page-header headline="Your membership"></ptu-page-header>
    <ptu-tabs>
        <ptu-tab href='/account'>Details</ptu-tab>
        <ptu-tab href='/account/tenancy'>Tenancy</ptu-tab>
        <ptu-tab href='/account/membership' selected>Membership</ptu-tab>
        <ptu-tab href='/account/contact-preferences'>Contact Preferences</ptu-tab>
    </ptu-tabs>

    <ptu-section sidebar="right">
        <ptu-box heading="Membership details" class="top-padding">

            @if($user->membership)
                <section class="value">
                    <header>
                        Membership Number
                    </header>
                    <p>
                        {{$user->membership->id}}
                    </p>
                </section>

                <section class="value">
                    <header>
                        Status
                    </header>
                    <div>
                        @switch($user->membership->status)
                            @case('active')
                                <ptu-chip colour="green" style="font-size: var(--fs-small)">{{ $user->membership->status }}</ptu-chip>
                                @break

                            @case('in-arrears')
                                <ptu-chip colour="yellow" style="font-size: var(--fs-small)">{{ $user->membership->status }}</ptu-chip>
                                @break

                            @case('new')
                                <ptu-chip colour="blue" style="font-size: var(--fs-small)">{{ $user->membership->status }}</ptu-chip>
                                @break

                            @default
                                <ptu-chip colour="red" style="font-size: var(--fs-small)">{{ $user->membership->status }}</ptu-chip>
                        @endswitch
                    </div>
                </section>

                <section class="value">
                    <header>Branch</header>
                    <p>{{ $user->branch->full_name }}</p>
                </section>

                <section class="value">
                    <header>Joined</header>
                    <div>
                        <p>{{ $user->membership->created_at->format("M Y") }}, {{ $user->membership->created_at->diffInMonths() }} months ago</p>
                    </div>
                </section>

                @if($user->membership->take_payments)
                    <section class="value">
                        <header>Subscription</header>
                        <aside>
                            <a href="/account/membership/change-rate">Change</a>
                        </aside>
                        <div>
                            <p>Monthly, you pay:</p>
                            <p style="font-size: var(--fs-h4)">
                                £ {{ number_format($user->membership->rate / 100, 2, '.', '') }}</p>
                        </div>
                    </section>

                    <section class="value">
                        <header>Payment day</header>
                        <aside>
                            <a href="/account/membership/change-payment-day">Change</a>
                        </aside>
                        <p>
                            {{$user->membership->payment_day}}<sup>{{\Carbon\Carbon::create('2020', '08', $user->membership->payment_day)->format("S")}}</sup>
                            of every month
                        </p>
                    </section>
                @else
                    <section class="value">
                        <header>Payment status</header>
                        <p>Payments are paused</p>
                    </section>
                @endif

                @php
                    $pm = $user->getPaymentMethod();
                @endphp
                @if($pm)
                    <section class="value">
                        <header>Payment method</header>
                        <aside>
                            <a href="/account/membership/update-payment-method">Change</a>
                        </aside>
                        <div>
                            <ptu-payment-method type="{{$pm['type']}}"
                                                brand="{{ $pm['brand'] }}"
                                                account-no="{{ $pm['last4'] }}"
                                                @isset($pm['sort_code'])
                                                    sort-code="{{$pm['sort_code']}}"
                                @endisset
                            ></ptu-payment-method>
                        </div>
                    </section>

                @endif
            @else
                <div class="card">
                    <header>
                        You don't seem to be a member.
                    </header>
                    <p>
                        We don't have any membership information for you, this might mean you
                        tried to join but didn't complete the process.
                    </p>
                    <footer>
                        <a href="/join">Become a member</a>
                    </footer>
                </div>
            @endif
        </ptu-box>

        <section style="margin-top: var(--layout-gap)">
            @include('blocks.payment-details')
            @include('blocks.branch-details')
        </section>

        @if($user->membership)
            <aside slot="sidebar">
                <ptu-membership-card name="{{ $user->full_name }}"
                                     membership-number="{{ $user->membership->id }}"></ptu-membership-card>
            </aside>
        @endif
    </ptu-section>

@endsection
