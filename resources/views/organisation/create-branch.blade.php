@extends('layouts.default')

@section('pageTitle', 'Organisation')

@section('content')

    <ptu-app-bar app="organisation">Organisation</ptu-app-bar>

    <ptu-app-layout page-title="Create branch"
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

        @if(count($errors) > 0)
            <article class="card prose danger" style="margin-bottom: var(--layout-gap)">
                <header>There was a problem:</header>
                <ul role="list">
                    @foreach($errors->all() as $error)
                        <li>{{$error}}</li>
                    @endforeach
                </ul>
            </article>
        @endif

        <section>
            <ptu-form action="{{route('org.branches')}}" method="post">
                @csrf
                <ptu-form-row label="Name" for="name">
                    <input id="name" type="text" name="name" size="25">
                </ptu-form-row>

                <ptu-form-row label="Description" for="description__control">
                    <ptu-textarea name='description' rows='4' maxchars="500"></ptu-textarea>
                </ptu-form-row>

                <ptu-form-row label="Postcodes" for="postcodes__control">
                    <ptu-textarea name='postcodes' rows='4'></ptu-textarea>
                    <p style="margin: 0 1ch">
                        Enter one postcode (or part of a postcode) per line.
                    </p>
                </ptu-form-row>

            </ptu-form>
        </section>
    </ptu-app-layout>
@endsection
