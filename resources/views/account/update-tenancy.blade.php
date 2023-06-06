@extends('layouts.default')

@section('pageTitle', 'Update your tenancy details')

@section('content')
    <ptu-page-header headline="Your tenancy"></ptu-page-header>
    <ptu-tabs>
        <ptu-tab href='/account'>Details</ptu-tab>
        <ptu-tab href='/account/tenancy' selected>Tenancy</ptu-tab>
        <ptu-tab href='/account/membership'>Membership</ptu-tab>
        <ptu-tab href='/account/contact-preferences'>Contact Preferences</ptu-tab>
    </ptu-tabs>

    @include('blocks.error-block')

    <ptu-section class="top-padding">
        <article class="prose ">
            <p>Use this form to create or update details of your current tenancy.</p>
        </article>
        <ptu-form action="/account/tenancy/update" method="POST">
            {{ csrf_field() }}
            <ptu-form-row label="Address">
                <input type="hidden"
                       name="uprn"
                       value="{{ $user->residentialInterest->address->uprn }}">
                <address
                    style="white-space: pre; font-style: normal; padding: .2em .4em">{{ $user->residentialInterest->address->getMultilineAddress() }}</address>
            </ptu-form-row>

            <ptu-form-row inline label="Start date"
                          description="This will usually be the day you moved in, but it's worth checking the tenancy agreement to be sure.">
                <label>
                    Day
                    <input size="4"
                           name="start_date[day]"
                           type="number"
                           min="1"
                           max="31"
                           maxlength="2"
                           value="{{ $user->residentialInterest->created_at->day }}"
                    >
                </label>
                <label>
                    Month
                    <input size="4"
                           name="start_date[month]"
                           type="number"
                           min="1"
                           max="12"
                           maxlength="2"
                           value="{{ $user->residentialInterest->created_at->month }}"
                    >
                </label>
                <label>
                    Year
                    <input size="7"
                           name="start_date[year]"
                           type="number"
                           min="1920"
                           max="2100"
                           maxlength="4"
                           value="{{ $user->residentialInterest->created_at->year }}"
                    >
                </label>
            </ptu-form-row>

            <ptu-form-row label="Tenancy type"
                          for="tenancy-type"
                          description="What type of tenancy agreement do you have?">
                <select id="tenancy-type" name="type">
                    <option value="ast"
                            @if($tenancy && $tenancy->type == 'ast')selected @endif
                    >
                        Assured shorthold
                    </option>
                    <option value="at"
                            @if($tenancy && $tenancy->type == 'at')selected @endif
                    >
                        Assured
                    </option>
                    <option value="flx"
                            @if($tenancy && $tenancy->type == 'flx')selected @endif
                    >
                        Flexible
                    </option>
                    <option value="pub"
                            @if($tenancy && $tenancy->type == 'pub')selected @endif
                    >
                        Public sector
                    </option>
                    <option value="reg"
                            @if($tenancy && $tenancy->type == 'reg')selected @endif
                    >
                        Regulated (protected)
                    </option>
                    <option value="emp"
                            @if($tenancy && $tenancy->type == 'emp')selected @endif
                    >
                        Provided by employer
                    </option>
                    <option value="mob"
                            @if($tenancy && $tenancy->type == 'mob')selected @endif
                    >
                        Occupier of a mobile home
                    </option>
                    <option value="lic"
                            @if($tenancy && $tenancy->type == 'lic')selected @endif
                    >
                        Excluded or licence
                    </option>
                    <option value=""
                            @if($tenancy && $tenancy->type == '')selected @endif
                    >
                        I don't know
                    </option>
                </select>
            </ptu-form-row>

            <ptu-form-row label="House in Multiple Occupation"
                          description="Is the property you live in a HMO?"
                          help-text="What is a HMO?"
            >
                <label class="option">
                    <input type="radio"
                           name="hmo"
                           value="1"
                           @if($tenancy && $tenancy->is_hmo)
                               checked
                        @endif
                    >
                    Yes
                </label>
                <label class="option">
                    <input type="radio"
                           name="hmo"
                           value="0"
                           @if($tenancy && $tenancy->is_hmo === false)
                               checked
                        @endif
                    >
                    No
                </label>
                <p slot="help">
                    You live in a HMO if there are multiple unconnected people living
                    in one property. Usually you'll each have your own tenancy and pay rent
                    separately, as well as having a lock on the door of your own room.
                </p>
            </ptu-form-row>

            <ptu-form-row label="Rent">
                <label>
                    How often do you pay your rent?
                    <select name="rent_period">
                        <option value="weekly"
                                @if($tenancy && $tenancy->rent_period == 'weekly')
                                    selected
                            @endif
                        >
                            Weekly
                        </option>
                        <option value="monthly"
                                @if($tenancy && $tenancy->rent_period == 'monthly')
                                    selected
                            @endif
                        >
                            Monthly
                        </option>
                        <option value="all"
                                @if($tenancy && $tenancy->rent_period == 'all')
                                    selected
                            @endif
                        >All up-front
                        </option>
                        <option value=""
                                @if($tenancy && $tenancy->rent_period == '')
                                    selected
                            @endif
                        >Other
                        </option>
                    </select>
                </label>
                <label>
                    How much is the rent (as stated in the tenancy agreement) (£)
                    <input type="number"
                           name="rent_amount"
                           size="6"
                           min="10"
                           maxlength="5"
                           @if($tenancy && $tenancy->rent_amount != 0)
                               value="{{$tenancy->rent_amount}}"
                        @endif
                    >
                </label>
            </ptu-form-row>

            <ptu-form-row label="Fixed-term">
                <label>
                    How many months is/was the initial fixed-term
                    <input size="4"
                           type="number"
                           maxlength="2"
                           name="initial_length"
                           @if($tenancy && $tenancy->initial_length != 0)
                               value="{{$tenancy->initial_length}}"
                        @endif
                    >
                </label>
                <label class="option">
                    <input type="checkbox"
                           name="not_fixed_term"
                           value="1"
                           @if($tenancy && $tenancy->initial_length === 0)
                               checked
                        @endif
                    >
                    Not a fixed term contract
                </label>
            </ptu-form-row>

            <ptu-form-row label="Deposit amount" for="deposit-amount" description="How much was your deposit? (£)">
                <input id="deposit-amount"
                       size="6"
                       type="number"
                       maxlength="4"
                       name="deposit_amount"
                       @if($tenancy && $tenancy->deposit_amount != 0)
                           value="{{$tenancy->deposit_amount}}"
                    @endif
                >
            </ptu-form-row>

            <ptu-form-row label="Deposit scheme" description="You can leave these blank if you don't know.">
                <label>
                    Deposit protection scheme name
                    <select name="dps_name">
                        <option value=""></option>
                        <option value="dps"
                                @if($tenancy && $tenancy->dps_name == 'dps')
                                    selected
                            @endif
                        >Deposit Protection Service
                        </option>
                        <option value="md"
                                @if($tenancy && $tenancy->dps_name == 'md')
                                    selected
                            @endif
                        >MyDeposits
                        </option>
                        <option value="tds"
                                @if($tenancy && $tenancy->dps_name == 'tds')
                                    selected
                            @endif
                        >Tenancy Deposit Scheme
                        </option>
                    </select>
                </label>
                <label>
                    Certificate reference number
                    <input type="text"
                           size="20"
                           name="dps_reference"
                           @if($tenancy && $tenancy->dps_reference)
                               value="{{$tenancy->dps_reference}}"
                        @endif
                    >
                </label>
            </ptu-form-row>

            <ptu-form-row label="Required documents" description="Has the landlord given you the following documents">
                <label class="option">
                    <input type="checkbox"
                           name="dps_status"
                           value="issued"
                           @if($tenancy && $tenancy->dps_status === 'issued')
                               checked
                           @endif
                    >
                    Deposit protection certificate
                </label>
                <label class="option">
                    <input type="checkbox"
                           name="htr_issued"
                           value="1"
                           @if($tenancy && $tenancy->htr_issued)
                               checked
                           @endif
                    >
                    How to rent booklet
                </label>
                <label class="option">
                    <input type="checkbox"
                           name="eps_issued"
                           value="1"
                           @if($tenancy && $tenancy->eps_issued)
                               checked
                           @endif
                    >
                    Energy performance certificate
                </label>
            </ptu-form-row>
        </ptu-form>
    </ptu-section>
@endsection
