@extends('layouts.default')

@section('pageTitle', 'Change your address')

@section('content')
    <ptu-page-header headline="Update your address"></ptu-page-header>
    <ptu-section>
        <dl>
            <dt>Selected Address</dt>
            <dd>
                <address style="white-space: pre">{{ $interest->address->getMultilineAddress() }}</address>
            </dd>
        </dl>
    </ptu-section>
    <ptu-section>
        <form action="/account/update-address/save" method="POST">
            {{ csrf_field() }}
            <input hidden name="interest_id" value="{{ $interest->id }}" />
            <ptu-fieldset label="When did you move into this address?" inline description="If you don't know the exact day, just enter the first day of the month.">
                <ptu-text-input width="4" name="moved_in[day]" label="Day" type="number"></ptu-text-input>
                <ptu-text-input width="4" name="moved_in[month]" label="Month" type="number"></ptu-text-input>
                <ptu-text-input width="7" name="moved_in[year]" label="Year" type="number"></ptu-text-input>
            </ptu-fieldset>

            <ptu-fieldset label="Which of these best describes your situation">
                <ptu-checkbox radio value="tenant" name="tenure" id="tenure_tenant">
                    <div>
                        <strong>I am a tenant</strong>
                        <p>I live here and I'm on the tenancy agreement</p>
                    </div>
                </ptu-checkbox>
                <ptu-checkbox radio value="owner-occupier" name="tenure" id="tenure_oo">
                    <div>
                        <strong>Owner/Occupier</strong>
                        <p>I live here and I own the property (or jointly own it)</p>
                    </div>
                </ptu-checkbox>
                <ptu-checkbox radio value="occupier" name="tenure" id="tenure_occupier">
                    <div>
                        <strong>Occupier</strong>
                        <p>I live here but I don't own it, and I'm not on the tenancy agreement</p>
                    </div>
                </ptu-checkbox>
                <ptu-checkbox radio value="licensee" name="tenure" id="tenure_licensee">
                    <div>
                        <strong>Licensee</strong>
                        <p>This is temporary accommodation</p>
                    </div>
                </ptu-checkbox>
            </ptu-fieldset>

            <footer>
                <button>Save</button>
            </footer>
        </form>
    </ptu-section>
@endsection
