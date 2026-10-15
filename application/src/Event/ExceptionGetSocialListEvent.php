<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ExceptionGetSocialListEvent extends Event
{
    private string $resultComment;
    private string $resultCode;

    public function __construct($resultComment, $resultCode,)
    {
        $this->resultComment = $resultComment;
        $this->resultCode = $resultCode;
    }

    /**
     * @return string
     */
    public function getResultComment(): string
    {
        return $this->resultComment;
    }

    /**
     * @return string
     */
    public function getResultCode(): string
    {
        return $this->resultCode;
    }
}
