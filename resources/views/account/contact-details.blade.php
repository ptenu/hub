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
        <ptu-box heading="Email addresses">
            <a class="primary-button button" style="font-size: var(--fs-small)" href="{{ route('account.new-email') }}" slot="actions">Add new</a>
            @if($user->emails()->exists())
                <table style="margin: 0">
                    <tbody>
                    @foreach($user->emails as $e)
                        <tr>
                            <th>{{$e->address}}</th>
                            <td>
                                @if($e->address == $user->email->address)
                                    <ptu-chip style="font-size: var(--fs-tiny)">Primary</ptu-chip>
                                @endif
                                @if($e->verified_at == null)
                                    <ptu-chip colour="red"  style="font-size: var(--fs-tiny)">Not verified</ptu-chip>
                                @endif
                            </td>
                            <td style="text-align: right">
                                <form action="{{route('account.delete.email', $e)}}" method="post">
                                    @method('DELETE')
                                    @csrf
                                    <ptu-confirm-button size="small" variant="danger" label="Delete">
                                        Are you sure you want to delete this email address?
                                    </ptu-confirm-button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p>There are no email addresses on record for you.</p>
            @endif

        </ptu-box>

        <ptu-box heading="Telephone numbers">
            <a class="primary-button button" style="font-size: var(--fs-small)"  href="{{route('account.new-tel')}}" slot="actions">Add new</a>
            @if($user->telephoneNumbers()->exists())
                <table style="margin: 0">
                    <tbody>
                    @foreach($user->telephoneNumbers as $t)
                        <tr>
                            <th>{{$t->national_number}}</th>
                            <td>
                                {{ucfirst($t->type)}}
                                @if($t->carrier)
                                    - {{$t->carrier}}
                                @endif
                            </td>
                            <td>
                                @if($t->number == $user->telephoneNumber->number)
                                    <ptu-chip style="font-size: var(--fs-tiny)">Primary</ptu-chip>
                                @endif
                                @if($t->verified_at == null)
                                    <ptu-chip colour="red" style="font-size: var(--fs-tiny)">Not verified</ptu-chip>
                                @endif
                            </td>
                            <td style="text-align: right">
                                <form action="{{route('account.delete.tel', $t)}}" method="POST">
                                    @method('DELETE')
                                    @csrf
                                    <ptu-confirm-button size="small" variant="danger" label="Delete">
                                        Are you sure you want to delete this number?
                                    </ptu-confirm-button>
                                </form>
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
