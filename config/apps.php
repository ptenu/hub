<?php

return [

    'web' => [
        'name' => "Public website",
        'path' => "/",
        'subdomain' => '',
        'roles' => [
            'guest',
            'user'
        ]
    ],

    'cms' => [
        'name' => "Content manager",
        'path' => "/cms",
        'subdomain' => 'app',
        'roles' => [
            'officer',
            'website-editor'
        ]
    ],

];
