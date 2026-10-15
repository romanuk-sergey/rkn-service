<?php

namespace App\Facade;

use Laminas\Soap\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\TransportException;

class RknFacadeV2 implements RknFacadeInterfaceV2
{
    private Client $client;

    public function __construct(
        private string $baseUrl,
        private string $login,
        private string $password,
        LoggerInterface $logger,
    )
    {
        $logger->error('RknFacadeV2 - url - ' . $baseUrl);
        $logger->error('RknFacadeV2 - login - ' . $login);
        $logger->error('RknFacadeV2 - password - ' . $password);


        // иначе не работает
        $arrContextOptions = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ];

        $this->client = new Client($this->baseUrl, ['stream_context' => stream_context_create($arrContextOptions)]);
        $this->client->setHttpLogin($this->login);
        $this->client->setHttpPassword($this->password);
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
     * @return array
     */
    public function getResultSocResources(): array
    {
        try {
            $response = $this->client->call('getResultSocResources');

            return (array)$response;
        } catch (\Throwable $e) {
            throw new TransportException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
