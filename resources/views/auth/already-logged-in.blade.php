@extends('layouts.default')

@section('pageTitle', 'Login')

@section('content')
    <ptu-page-header headline="Login" class="bottom-padding">
        <p>Access your account</p>
    </ptu-page-header>

    <ptu-section sidebar="right">
        <ptu-complete headline="You are already logged in">You do not need to log in again.</ptu-complete>
    </ptu-section>
@endsection
