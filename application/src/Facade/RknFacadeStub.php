<?php

namespace App\Facade;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class RknFacadeStub implements RknFacadeInterface
{
    public function __construct(
        private string $baseUrl,
    )
    {
    }

    public function getLastDumpDateEx(): array
    {
        return [
            "lastDumpDate" => 1646292600000,
            "lastDumpDateUrgently" => 1646292600000,
            "lastDumpDateSocResources" => 1646292600000,
            "webServiceVersion" => "3.1",
            "dumpFormatVersion" => "2.4",
            "dumpFormatVersionSocResources" => "1.0",
            "docVersion" => "4.9",
        ];
    }

    public function sendRequest(UploadedFile $requestFile, UploadedFile $signatureFile): array
    {
        return [
            "result" => true,
            "resultComment" => "запрос принят",
            "code" => "8602c5513f88008fc01b68968e18b7452",
        ];
    }

    public function getResultSocResources(string $code): array
    {
        if ($code === '666') {
            return [
                'result' => false,
                'resultComment' => 'не найден запрос по указанному идентификатору',
                'resultCode' => -9,
            ];
        }

        return [
            'result' => true,
            'registerZipArchive' => 'base64 file',
            'resultCode' => 1,
            'dumpFormatVersion' => '1.0',
            'operatorName' => 'ТЕСТ',
            'inn' => '1234567890',
        ];
    }
}
