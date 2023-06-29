<!DOCTYPE html>
<html dir='ltr' lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset='utf-8'/>
    <meta name='viewport' content='width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=5.0'/>
    <title>@yield('pageTitle', 'Untitled page') - {{ $siteName }}</title>

    <link rel='stylesheet' href='https://static.peterboroughtenants.app/elements/dist/ptu-elements/ptu-elements.css'/>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link
        href="https://fonts.bunny.net/css?family=atkinson-hyperlegible:400,400i,700,700i|sofia-sans:300,400,500,600,700,800,900"
        rel="stylesheet"/>

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --brand-font: 'Atkinson Hyperlegible';
            --header-font: 'Atkinson Hyperlegible';
            --border-radius: 0;
        }
    </style>

    <script type="module"
            src="https://static.peterboroughtenants.app/elements/dist/ptu-elements/ptu-elements.esm.js"></script>

    @stack('head-js')
</head>

<body>
<ptu-navbar
    @auth
        show-apps
    user-name="{{ Auth::user()->full_name }}"
    @endauth
>
    @auth
        <section slot="user-menu">
            <ul>
                <li>
                    <a href="/account" class="nav-link">Account details</a>
                </li>
                <li>
                    <a href="/account/membership" class="nav-link">Membership details</a>
                </li>
                <li>
                    <a href="/account/contact-preferences" class="nav-link">Contact preferences</a>
                </li>
                <li>
                    <form method="post" action="/logout">
                        {{ csrf_field() }}
                        <button>Logout</button>
                    </form>
                </li>
            </ul>
        </section>
    @endauth

    <ptu-grid>
        <section>
            <hgroup>
                <h5>About us</h5>
            </hgroup>
        </section>
        <section>
            <hgroup>
                <h5>Guides & Information</h5>
            </hgroup>
        </section>
        <section>
            <hgroup>
                <h5>For members</h5>
            </hgroup>
            <ul>
                @auth
                    <li>
                        <a href="/dashboard" class="nav-link">Dashboard</a>
                    </li>
                @endauth
                @guest
                    <li>
                        <a href="/login" class="nav-link">Login</a>
                    </li>
                @endguest
            </ul>
        </section>
    </ptu-grid>

    @auth
        <ptu-grid slot="apps">
            <ptu-app-icon app="web" href="/">Main Website</ptu-app-icon>
            <ptu-app-icon app="mail" href="/">Messages</ptu-app-icon>
            <ptu-app-icon app="forum" href="/">Forum</ptu-app-icon>
            <ptu-app-icon app="wiki" href="/">Wiki</ptu-app-icon>
            <ptu-app-icon app="learning" href="/">Learning Centre</ptu-app-icon>
        </ptu-grid>
    @endauth
</ptu-navbar>

@if(session()->has("status"))
    <ptu-section class="top-padding bottom-padding">
        <div class="card @if(session()->has("status-class")){{ session()->get('status-class')}}@endif">
            {{session()->get("status")}}
        </div>
    </ptu-section>
@endif

@if(session()->has("error"))
    <ptu-section class="top-padding bottom-padding">
        <div style="background-color: var(--colour-red-100)" class="card @if(session()->has("status-class")){{ session()->get('status-class')}}@endif">
            {{session()->get("error")}}
        </div>
    </ptu-section>
@endif

@yield('content')

@include('layouts.footer')

@stack('body-js')
</body>

</html>
