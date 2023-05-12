@extends('layouts.default')

@section('pageTitle', 'Enter your password')

@section('content')
    <ptu-page-header headline="Login" class="bottom-padding">
        <p>Access your account</p>
    </ptu-page-header>

    <ptu-section sidebar="right">
        <form method="post" action="/authenticate" >
            {{ csrf_field() }}
            <ptu-fieldset label="Email">
                <input name="email" value="{{$email}}" hidden>
                <p>
                    {{$email}}
                    <a href="/login">(Change)</a>
                </p>
            </ptu-fieldset>

            <ptu-fieldset label="One-time password" @error('password')error-text="{{$message}}"@enderror>
                <ptu-password-input label="One-time password"
                                show-label="false"
                                name="password"
                                type="password"
                                autocomplete="one-time-code"
                                width="30"></ptu-password-input>
            </ptu-fieldset>

            <p>
                <button>Login</button>
            </p>
        </form>
    </ptu-section>

    <ptu-section class="prose" sidebar="right">
        <ptu-details summary="Why haven't I received a password?">
            <p>
                Your single use password will be sent to the email you logged in with. It can take a minute or two
                for this email to arrive, so please wait. If it hasn't come after 4 or 5 minutes, try logging in again
                and you'll be sent a new password.
            </p>
            <p>
                For security reasons, we won't tell you if your email is incorrect, but we don't
                send passwords to emails unless they're registered to you on our system - therefore you should also check that
                the email you've entered is correct and is the same one you have registered with us.
            </p>
        </ptu-details>
    </ptu-section>
@endsection
