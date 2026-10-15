<?php

namespace App\Facade;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface RknFacadeInterface
{
    public function getLastDumpDateEx(): array;

    public function sendRequest(UploadedFile $requestFile, UploadedFile $signatureFile): array;

    public function getResultSocResources(string $code): array;
}
