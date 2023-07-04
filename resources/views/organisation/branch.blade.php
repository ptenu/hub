@extends('layouts.default')

@section('pageTitle', $branch->name .' Branch')

@section('content')

    <ptu-app-bar app="organisation">Organisation</ptu-app-bar>

    <ptu-app-layout page-title="{{$branch->name}} Branch"
                    app="organisation"
                    app-href="{{route('org.index')}}"
                    section="Branches"
                    section-href="{{route('org.branches')}}"
    >
        <nav slot="menu">
            <a href="{{route('org.members')}}">Members</a>
            <a href="{{route('org.branches')}}">
                <strong>Branches</strong>
            </a>
            <a href="{{route('org.events')}}">Events</a>
        </nav>

        <form method="post" slot="actions">
            @method('delete')
            @csrf
            <ptu-confirm-button variant="danger" label="Delete Branch">
                <div class="prose" style="font-size: var(--fs-base)">
                    <p>Are you sure? This branch will be archived.</p>
                </div>
            </ptu-confirm-button>
        </form>

        <ptu-box heading="Details">
            <a href="{{route('org.branch.edit', [$branch])}}" slot="actions">Edit</a>
            <section class="value">
                <header>
                    Full name
                </header>
                <p>
                    {{$branch->full_name}}
                </p>
            </section>
            <section class="value">
                <header>
                   Description
                </header>
                <p>
                    {{$branch->description}}
                </p>
            </section>
            <section class="value">
                <header>
                    Number of members
                </header>
                <p>
                    {{count($branch->members)}}
                </p>
            </section>
        </ptu-box>

        <ptu-box heading="Officers">
            <a href="/" slot="actions">Assign Officer</a>
            @if($branch->officers()->count() > 0)
                <table style="margin: 0">
                    <tr>
                        <th>
                            Name
                        </th>
                        <th>
                            Position
                        </th>
                        <th>
                            Date started
                        </th>
                    </tr>
                    @foreach($branch->officers as $officer)
                        <tr>
                            <td>{{$officer->full_name}}</td>
                            <td>
                                {{$officer->pivot->title}}
                            </td>
                            <td>
                                {{$officer->pivot->starts_on}}
                            </td>
                        </tr>
                    @endforeach
                </table>
            @else
                <p>There are currently no officers in this branch.</p>
            @endif
        </ptu-box>
    </ptu-app-layout>
@endsection
