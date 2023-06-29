<?php

namespace App\Extensions;

use Twilio\Rest\Client;

class TwillioService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client(
            env('TWILLIO_ACCOUNT_ID'),
            env('TWILLIO_AUTH_TOKEN')
        );
    }

    public static function getClient()
    {
        $service = new TwillioService();
        return $service->client;
    }

}
