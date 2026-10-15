<?php

namespace App\Message;

/**
 * Class GetSocialListMessage
 * @package App\Message
 */
class GetSocialListMessage
{
    public function __construct(private string $code)
    {
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }
}