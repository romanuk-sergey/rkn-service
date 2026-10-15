<?php

namespace App\Event;

use App\Entity\Dto\Xml\ChangesSocList;
use Symfony\Contracts\EventDispatcher\Event;

class SuccessfulGetSocialListNotificationEvent extends Event
{
    private ChangesSocList $changesSocList;

    /**
     * @param $changesSocList
     */
    public function __construct($changesSocList)
    {
        $this->changesSocList = $changesSocList;
    }

    /**
     * @return ChangesSocList
     */
    public function getChangesSocList(): ChangesSocList
    {
        return $this->changesSocList;
    }
}
