<!DOCTYPE html>
<html dir='ltr' lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset='utf-8'/>
    <meta name='viewport' content='width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=5.0'/>
    <title>@yield('pageTitle', 'Untitled page') - {{ $siteName }}</title>

    <link rel='stylesheet' href='https://static.peterboroughtenants.app/elements/dist/ptu-elements/ptu-elements.css'/>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=atkinson-hyperlegible:400,400i,700,700i|sofia-sans:300,400,500,600,700,800,900" rel="stylesheet" />

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --brand-font: 'Atkinson Hyperlegible';
            --header-font: 'Sofia Sans';
            --border-radius: 0;
        }
    </style>

    <script type="module"
            src="https://static.peterboroughtenants.app/elements/dist/ptu-elements/ptu-elements.esm.js"></script>

    @stack('head-js')
</head>

<body>
<ptu-navbar>
    <ptu-logo></ptu-logo>
    <ptu-visibility-toggle slot='right' element-id='main-menu'>Menu</ptu-visibility-toggle>
    <ptu-user-button slot="right"
                     @auth
                         authenticated="true"
                         href="/dashboard"
                     @endauth
                     @guest href="/login" @endguest>
        @auth
            {{ Auth::user()->full_name }}
        @endauth
        @guest
            Login
        @endguest
    </ptu-user-button>
</ptu-navbar>
<ptu-nav-menu id='main-menu'>
    @auth
        <div class="card"
             style="display: flex; justify-content: space-between; align-items: center; grid-column: 1 / -1; background-color: rgba(10,10,10,0.18)">
            <strong>
                Hi {{ Auth::user()->given_name }}. <a href="/account">Edit your details</a>.
            </strong>
            <aside>
                <form method="post" action="/logout">
                    {{ csrf_field() }}
                    <button>Logout</button>
                </form>
            </aside>
        </div>
    @endauth
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
    </section>
</ptu-nav-menu>

@if(session()->has("status"))
    <ptu-section class="top-padding bottom-padding">
        <div class="card @if(session()->has("status-class")){{ session()->get('status-class')}}@endif">
            {{session()->get("status")}}
        </div>
    </ptu-section>
@endif

@yield('content')

@include('layouts.footer')

@stack('body-js')
</body>

</html>
