<?php

namespace App\Event;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Contracts\EventDispatcher\Event;

class SendEmailEvent extends Event
{
    private TemplatedEmail $email;

    /**
     * @param TemplatedEmail $email
     */
    public function __construct(TemplatedEmail $email)
    {
        $this->email = $email;
    }

    /**
     * @return TemplatedEmail
     */
    public function getEmail(): TemplatedEmail
    {
        return $this->email;
    }
}
