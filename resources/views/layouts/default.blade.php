<!DOCTYPE html>
<html dir='ltr' lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset='utf-8' />
    <meta name='viewport' content='width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=5.0' />
    <title>@yield('pageTitle', 'Untitled page') - {{ $siteName }}</title>

    <link rel='stylesheet' href='https://static.peterboroughtenants.app/elements/dist/ptu-elements/ptu-elements.css' />
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sofia-sans:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />
    <style>
        :root {
            --brand-font: 'Sofia Sans';
        }
    </style>

    <script type="module" src="https://static.peterboroughtenants.app/elements/dist/ptu-elements/ptu-elements.esm.js"></script>

</head>

<body>
<ptu-navbar>
    <ptu-logo variant='colour'></ptu-logo>
    <ptu-visibility-toggle slot='right' element-id='main-menu'>Menu</ptu-visibility-toggle>
    <ptu-user-button slot="right"
                     @auth authenticated="true" @endauth
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
</ptu-nav-menu>

@yield('content')

@include('layouts.footer')

</body>

</html>
