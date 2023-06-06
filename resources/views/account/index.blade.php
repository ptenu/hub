@extends('layouts.default')

@section('pageTitle', 'Account')

@section('content')

    <ptu-page-header headline="Your account"></ptu-page-header>
    <ptu-tabs>
        <ptu-tab href='/account' selected>Details</ptu-tab>
        <ptu-tab href='/account/tenancy'>Tenancy</ptu-tab>
        <ptu-tab href='/account/membership'>Membership</ptu-tab>
        <ptu-tab href='/account/contact-preferences'>Contact Preferences</ptu-tab>
    </ptu-tabs>

    <ptu-section class="prose">
        <h5>Personal Details</h5>
        <p>
            These are the details we have on file for you. Please make sure you keep them up to date.
            Use the links in the actions menu to make any changes as needed.
        </p>
    </ptu-section>

    <ptu-section sidebar="right">
        <aside slot="sidebar">
            <ul class="list">
                <li>
                    <a href="/account/update-details">Edit your details</a>
                </li>
                <li>
                    <a href="/account/update-address">Update your address</a>
                </li>
            </ul>
        </aside>
        <dl>
            <dt>Name</dt>
            <dd>{{ $user->full_name }}</dd>

            @if($user->membership()->exists())
                <dt>Membership number</dt>
                <dd>{{ $user->membership->id }}</dd>
            @endif

            <dt>Primary email</dt>
            <dd>{{ $user->email->address }}</dd>

            @if($user->telephoneNumber)
                <dt>Telephone number</dt>
                <dd>{{ $user->telephoneNumber->number }}</dd>
            @endif

            <dt>Address</dt>
            @if(is_null($user->residentialInterest))
                <dd>No address on record</dd>
            @endif
            @if(isset($user->residentialInterest))
                <dd>
                    <address
                        style="font-style: normal; white-space: pre">{{$user->residentialInterest->address->getMultiLineAddress()}}</address>
                </dd>
                <dt>Occupancy status</dt>
                <dd>
                    {{ ucwords(str_replace("-", " / ", $user->residentialInterest->type)) }}
                </dd>
            @endif

            @if($user->branch)
                <dt>Branch</dt>
                <dd>{{$user->branch->full_name}}</dd>
            @endif

            @if(isset($user->date_of_birth))
                <dt>Date of Birth</dt>
                <dd>{{ $user->date_of_birth->format("jS M Y") }}</dd>
            @endif

            @if(isset($user->first_language))
                <dt>First language</dt>
                <dd>{{ $user->getLanguageName() }}</dd>
            @endif

            @if(isset($user->legal_sex))
                <dt>Legal sex</dt>
                <dd>
                    @if($user->legal_sex == "M")
                        Male
                    @endif
                    @if($user->legal_sex == "F")
                        Female
                    @endif
                </dd>
            @endif
        </dl>
    </ptu-section>

@endsection
