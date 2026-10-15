<?php

namespace App\Services;

use App\Facade\RknFacadeInterfaceV2;

/**
 * Class RknService
 * @package App\Services
 */
class RknServiceV2
{

    public function __construct(private RknFacadeInterfaceV2 $rknFacade)
    {
    }

    public function getLastSocDate(): array
    {
        return $this->rknFacade->getLastDumpDateEx();
    }

    public function getSocList(): array
    {
        return $this->rknFacade->getResultSocResources();
    }
}