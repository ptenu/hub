@extends('layouts.default')

@section('pageTitle', 'Your contact details and preferences')

@section('content')

    <ptu-page-header headline="Contact Preferences"></ptu-page-header>
    <ptu-tabs>
        <ptu-tab href='/account'>Details</ptu-tab>
        <ptu-tab href='/account/tenancy'>Tenancy</ptu-tab>
        <ptu-tab href='/account/membership'>Membership</ptu-tab>
        <ptu-tab href='/account/contact-preferences' selected>Contact Preferences</ptu-tab>
    </ptu-tabs>

    <ptu-section sidebar="right">
        <section class="top-padding">
            <hgroup style="border: none">
                <h4>Email addresses</h4>
            </hgroup>
            <button class="primary-button">Make primary</button>
            <button>Delete</button>

            @if($user->emails()->exists())
                <table>
                    <thead>
                    <tr>
                        <th colspan="3">Email</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($user->emails as $e)
                        <tr>
                            <td>
                                <input type="radio" name="email" value="{{ $e->id }}">
                            </td>
                            <th>{{$e->address}}</th>
                            <td>
                                @if($e->address = $user->email->address)
                                    <ptu-chip>Primary</ptu-chip>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
            <div class="card">
                <header>There are no email addresses on record for you.</header>
            </div>
            @endif

        </section>

        <section class="top-padding">
            <hgroup>
                <h4>Telephone numbers</h4>
            </hgroup>
            @if($user->telephoneNumbers()->exists())
                <table>
                    <thead>
                    <tr>
                        <th colspan="3">Email</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($user->telephoneNumbers as $t)
                        <tr>
                            <td>
                                <input type="radio" name="email" value="{{ $t->id }}">
                            </td>
                            <th>{{$t->number}}</th>
                            <td>
                                @if($t->number = $user->telephoneNumber->number)
                                    <ptu-chip>Primary</ptu-chip>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="card">
                    <header>There are no telephone numbers on record for you.</header>
                </div>
            @endif
        </section>
    </ptu-section>
@endsection
