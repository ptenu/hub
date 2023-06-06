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
        <section class="top-padding">
            <hgroup style="border: none">
                <h4>Membership details</h4>
            </hgroup>

            @if($user->membership)
                <dl>
                    <dt>
                        Membership Number
                    </dt>
                    <dd>
                        {{$user->membership->id}}
                    </dd>
                    <dt>Status</dt>
                    <dd>
                        @switch($user->membership->status)
                            @case('active')
                                <ptu-chip colour="green">{{ $user->membership->status }}</ptu-chip>
                                @break

                            @case('in-arrears')
                                <ptu-chip colour="yellow">{{ $user->membership->status }}</ptu-chip>
                                @break

                            @case('new')
                                <ptu-chip colour="blue">{{ $user->membership->status }}</ptu-chip>
                                @break

                            @default
                                <ptu-chip colour="red">{{ $user->membership->status }}</ptu-chip>
                        @endswitch
                    </dd>

                    <dt>
                        Branch
                    </dt>
                    <dd>
                        {{ $user->branch->full_name }}
                    </dd>

                    <dt>
                        Joined
                    </dt>
                    <dd>
                        {{ $user->membership->created_at->format("M Y") }}
                    </dd>
                    <dd>
                        {{ $user->membership->created_at->diffInMonths() }} months ago
                    </dd>

                    @if($user->membership->take_payments)
                        <dt>Rate</dt>
                        <dd>Monthly, you pay:</dd>
                        <dd style="font-size: var(--fs-h4)">
                            £ {{ number_format($user->membership->rate / 100, 2, '.', '') }}
                        </dd>
                        <dd>
                            <a href="/account/membership/change-rate">Change how much you pay</a>
                        </dd>

                        <dt>Payment day</dt>
                        <dd>
                            {{$user->membership->payment_day}}<sup>{{\Carbon\Carbon::create('2020', '08', $user->membership->payment_day)->format("S")}}</sup>
                            of every month
                        </dd>
                        <dd>
                            <a href="/account/membership/change-payment-day">Change which day you're charged</a>
                        </dd>
                    @else
                        <dt>Rate</dt>
                        <dd>Payments are paused</dd>
                    @endif

                    @php
                    $pm = $user->getPaymentMethod();
                    @endphp
                    @if($pm)
                        <dt>Payment method</dt>
                        <dd>
                            <ptu-payment-method type="{{$pm['type']}}"
                                                brand="{{ $pm['brand'] }}"
                                                account-no="{{ $pm['last4'] }}"
                                                @isset($pm['sort_code'])
                                                    sort-code="{{$pm['sort_code']}}"
                                @endisset
                            ></ptu-payment-method>
                        </dd>
                        <dd>
                            <a href="/account/membership/update-payment-method">Change payment method</a>
                        </dd>
                    @endif
                </dl>
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
        </section>

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
