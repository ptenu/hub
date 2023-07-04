@extends('layouts.default')

@section('pageTitle', 'Branches')

@section('content')

    <ptu-app-bar app="organisation">Organisation</ptu-app-bar>

    <ptu-app-layout page-title="Branches"
                    app="organisation"
                    app-href="{{route('org.index')}}"
                    section="Branches"
    >
        <nav slot="menu">
            <a href="{{route('org.members')}}">Members</a>
            <a href="{{route('org.branches')}}">
                <strong>Branches</strong>
            </a>
            <a href="{{route('org.events')}}">Events</a>
        </nav>

        <a href="{{route('org.branches.create')}}"
           class="button primary-button"
           slot="actions"
        >
            New Branch
        </a>

        <section class="top-padding">
            <ul class="columns">
                @foreach($branches as $branch)
                    <li>
                        <a href="{{route('org.branch', [$branch])}}">
                            {{$branch->full_name}}
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    </ptu-app-layout>
@endsection
