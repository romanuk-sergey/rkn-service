<?php

namespace App\Facade;

use Laminas\Soap\Client;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RknFacade implements RknFacadeInterface
{
    private const DUMP_FORMAT_VERSION = '2.4';

    private Client $client;

    public function __construct(
        private string          $baseUrl,
    )
    {
        // todo используется только для доступа к тестовой ссылке wsdl. Скорее всего решается на стороне devops
        $arrContextOptions = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ];

        $this->client = new Client($this->baseUrl, ['stream_context' => stream_context_create($arrContextOptions)]);
    }

    /**
     * @return array
     */
    public function getLastDumpDateEx(): array
    {
        try {
            $response = $this->client->call('getLastDumpDateEx');

            return (array)$response;
        } catch (\Throwable $e) {
            throw new TransportException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param UploadedFile $requestFile
     * @param UploadedFile $signatureFile
     * @return array
     */
    public function sendRequest(UploadedFile $requestFile, UploadedFile $signatureFile): array
    {
        try {
            $response = $this->client->call('sendRequest', ['request' => [
                'requestFile' => $requestFile->getContent(),
                'signatureFile' => $signatureFile->getContent(),
                'dumpFormatVersion' => self::DUMP_FORMAT_VERSION,
            ]]);

            return (array)$response;
        } catch (\Throwable $e) {
            throw new TransportException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $code
     * @return array
     */
    public function getResultSocResources(string $code): array
    {
        try {
            $response = $this->client->call('getResultSocResources', ['request' => [
                'code' => $code,
            ]]);

            return (array)$response;
        } catch (\Throwable $e) {
            throw new TransportException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
