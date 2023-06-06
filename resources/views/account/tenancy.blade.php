@extends('layouts.default')

@section('pageTitle', 'Your tenancy details')

@section('content')

    <ptu-page-header headline="Your tenancy"></ptu-page-header>
    <ptu-tabs>
        <ptu-tab href='/account'>Details</ptu-tab>
        <ptu-tab href='/account/tenancy' selected>Tenancy</ptu-tab>
        <ptu-tab href='/account/membership'>Membership</ptu-tab>
        <ptu-tab href='/account/contact-preferences'>Contact Preferences</ptu-tab>
    </ptu-tabs>

    <ptu-section class="top-padding" @if($tenancy)sidebar="right"@endif>
        <nav slot="sidebar">
            <ul class="list">
                <li>
                    <a href="/account/tenancy/update">Update tenancy information</a>
                </li>
            </ul>
        </nav>

        <section class="prose">
            <p>This page contains the information we have about your tenancy.</p>

            @if(!$user->residentialInterest)
                <article class="card">
                    <header>
                        There is no address on file for you.
                    </header>
                    <p>Before you can add any information about your tenancy, you'll need to add an address to your
                        account.</p>
                    <footer>
                        <a href="/account/update-address">Update your address</a>
                    </footer>
                </article>
            @else
            <dl>
                    <dt>Current residential address</dt>
                    <dd style="white-space: pre">{{$user->residentialInterest->address->getMultiLineAddress()}}</dd>
                    <dd><a href="/account/update-address">Change address</a></dd>
                    <dt>Property type</dt>
                    <dd>{{$user->residentialInterest->address->classification}}</dd>
                    @if($tenancy && $tenancy->is_hmo !== null)
                        <dt>Is property a HMO</dt>
                        <dd>
                            @if($tenancy->is_hmo)
                                Yes
                            @else
                                No
                            @endif
                        </dd>
                    @endif
            </dl>
            @endif

            @if($user->residentialInterest->type != 'tenant')
                <article class="card" style="margin-top: var(--layout-gap)">
                    <header>
                        You are not listed as a tenant.
                    </header>
                    <p>
                        This means you cannot add tenancy information to your current residential address.
                    </p>
                    <p>If you think this is a mistake, please get in touch.</p>
                </article>
            @endif

            @if(!$tenancy && $user->residentialInterest && $user->residentialInterest->type == 'tenant')
                <article class="card" style="margin-top: var(--layout-gap)">
                    <header>
                        There is no information on file for your current tenancy.
                    </header>
                    <p>
                        It's a good idea to add this information now, as it will speed things up if we need to provide
                        support to you in the future.
                    </p>
                    <footer>
                        <a href="/account/tenancy/update">Add tenancy information</a>
                    </footer>
                </article>
            @endif

            @if($tenancy)
                <article>
                    <h4 style="border: none; padding: 0">Contract</h4>
                    <dl>
                        <dt>Start date</dt>
                        <dd>{{$tenancy->start_date->format("jS M Y")}}</dd>

                        @if($tenancy->initial_length != 0)
                            <dt>Term</dt>
                            <dd>{{$tenancy->initial_length}} months</dd>
                        @endif

                        @if($tenancy->type)
                            <dt>Tenancy type</dt>
                            <dd>
                                {{$tenancy->type_name}}
                            </dd>
                        @endif

                        @if($tenancy->rent_amount !== null)
                            <dt>Rent</dt>
                            <dd>£{{$tenancy->rent_amount}}</dd>
                            @if(in_array($tenancy->rent_period, ['weekly', 'monthly']))
                                <dd>Paid {{$tenancy->rent_period}}</dd>
                            @endif
                        @endif
                    </dl>

                    @if($tenancy->dps_status !== null)
                        <h4 style="border: none; padding: 0">Deposit</h4>
                        <dl>
                            <dt>Deposit amount paid</dt>
                            <dd>£{{$tenancy->deposit_amount}}</dd>

                            <dt>Deposit protection scheme</dt>
                            @switch($tenancy->dps_name)
                                @case('tds')
                                    <dd>Tenancy Deposit Scheme</dd>
                                    @break
                                @case('dps')
                                    <dd>Deposit Protection Service</dd>
                                    @break
                                @case('md')
                                    <dd>MyDeposits</dd>
                            @endswitch

                            <dt>Deposit certificate reference</dt>
                            <dd>{{$tenancy->dps_reference}}</dd>
                        </dl>
                    @endif

                </article>
            @endif
        </section>
    </ptu-section>
    <ptu-section class="top-padding">
        <hgroup>
            <h2 style="margin-top: var(--layout-gap)">Previous tenancies</h2>
        </hgroup>

        @if(!$user->previousTenancies()->exists())
            <article class="card">
                <header>No previous tenancies</header>
                <p>There are currently no previous tenancies on record for you.</p>
            </article>
        @else
            <table>
                <tbody>
                @foreach($user->previousTenancies as $t)
                    <tr>
                        <td>{{ $t->start_date->year }} - {{ $t->end_date->year }}</td>
                        <td>{{ $t->address->getSingleLineAddress() }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </ptu-section>
@endsection
