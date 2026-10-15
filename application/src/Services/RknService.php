<?php

namespace App\Services;

use App\Facade\RknFacadeInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class RknService
 * @package App\Services
 */
class RknService
{

    public function __construct(private RknFacadeInterface $rknFacade)
    {
    }

    public function getLastSocDate(): array
    {
        return $this->rknFacade->getLastDumpDateEx();
    }

    public function sendRequest(UploadedFile $requestFile, UploadedFile $signatureFile): array
    {
        return $this->rknFacade->sendRequest($requestFile, $signatureFile);
    }

    public function getSocList($code): array
    {
        return $this->rknFacade->getResultSocResources($code);
    }
}