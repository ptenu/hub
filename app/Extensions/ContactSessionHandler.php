<?php

namespace App\Extensions;

use \Illuminate\Session\DatabaseSessionHandler;

class ContactSessionHandler extends DatabaseSessionHandler
{
    protected function addUserInformation(&$payload)
    {
        if ($this->container->bound(Guard::class)) {
            $payload['contact_id'] = $this->userId();
        }

        return $this;
    }
}
