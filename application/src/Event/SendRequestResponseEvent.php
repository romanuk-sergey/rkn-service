<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class SendRequestResponseEvent extends Event
{
    private array $response;

    /**
     * @param $response
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    public function getResponse(): array
    {
        return $this->response;
    }
}
