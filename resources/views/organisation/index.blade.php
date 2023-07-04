@extends('layouts.default')

@section('pageTitle', 'Organisation')

@section('content')

    <ptu-app-bar app="organisation">Organisation</ptu-app-bar>

    <ptu-app-layout page-title="Organisation" app="organisation" app-href="{{route('org.index')}}">
        <nav slot="menu">
            <a href="{{route('org.members')}}">Members</a>
            <a href="{{route('org.branches')}}">Branches</a>
            <a href="{{route('org.events')}}">Events</a>
        </nav>

        <ptu-grid>
            <a class="card" href="{{route('org.members')}}">
                <header>Members</header>
                <p>Search and manage members' information.</p>
            </a>
            <a class="card" href="{{route('org.branches')}}">
                <header>Branches</header>
                <p>See a list of the Union's branches and the officers for each branch.</p>
            </a>
            <a class="card" href="{{route('org.events')}}">
                <header>Events</header>
                <p>See and manage events and meetings</p>
            </a>
        </ptu-grid>
    </ptu-app-layout>
@endsection
