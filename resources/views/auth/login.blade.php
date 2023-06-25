@extends('layouts.default')

@section('pageTitle', 'Login')

@section('content')
    <ptu-page-header headline="Login" class="bottom-padding">
        <p>Access your account</p>
    </ptu-page-header>

    <ptu-section sidebar="right">
        <form method="post">
            {{ csrf_field() }}
            <ptu-fieldset label="Email"
                          @error('email')error-text="{{$message}}"@enderror
                          description="The email address linked to your PTU membership">
                <ptu-text-input label="Your email address"
                                name="email"
                                inputmode="email"
                                autocomplete="email"
                                width="30"
                                show-label="false"></ptu-text-input>
            </ptu-fieldset>
            <p>
                When you click to proceed, we'll send you a temporary password which you can use this once to log in.
            </p>
            <footer class="top-padding">
                <button class="primary-button">Send password</button>
            </footer>
        </form>
    </ptu-section>
@endsection
