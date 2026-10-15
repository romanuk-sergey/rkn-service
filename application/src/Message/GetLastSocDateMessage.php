<?php

namespace App\Message;

/**
 * Class GetLastSocDateMessage
 * @package App\Message
 */
class GetLastSocDateMessage
{
    public function __construct(private int $date)
    {
    }

    /**
     * @return int
     */
    public function getDate(): int
    {
        return $this->date;
    }
}