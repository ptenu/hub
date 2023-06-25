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
        <ptu-box heading="Email addresses" style="margin: var(--layout-gap) 0;">
            <a href="/" slot="actions">Add new</a>
            @if($user->emails()->exists())
                <table style="margin: 0">
                    <tbody>
                    @foreach($user->emails as $e)
                        <tr>
                            <th>{{$e->address}}</th>
                            <td>
                                @if($e->address = $user->email->address)
                                    <ptu-chip style="font-size: var(--fs-small)">Primary</ptu-chip>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p>There are no email addresses on record for you.</p>
            @endif

        </ptu-box>

        <ptu-box heading="Telephone numbers" style="margin: var(--layout-gap) 0;">
            <a href="/" slot="actions">Add new</a>
            @if($user->telephoneNumbers()->exists())
                <table style="margin: 0">
                    <tbody>
                    @foreach($user->telephoneNumbers as $t)
                        <tr>
                            <th>{{$t->number}}</th>
                            <td>
                                @if($t->number = $user->telephoneNumber->number)
                                    <ptu-chip style="font-size: var(--fs-small)">Primary</ptu-chip>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p>There are no telephone numbers on record for you.</p>
            @endif
        </ptu-box>
    </ptu-section>
@endsection
