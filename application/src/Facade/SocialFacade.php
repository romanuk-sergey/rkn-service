<?php

namespace App\Facade;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class TelegramFacade
 * @package App\Facade
 */
class SocialFacade
{
    /**
     * @param string $telegramUrl
     * @param string $trueconfUrl
     * @param string $token
     * @param string $telegramChatId
     * @param string $trueconfChatId
     * @param HttpClientInterface $client
     */
    public function __construct(
        private string              $telegramUrl,
        private string              $trueconfUrl,
        private string              $token,
        private string              $telegramChatId,
        private string              $trueconfChatId,
        private HttpClientInterface $client,
    )
    {
    }

    /**
     * @param string $text
     * @throws TransportExceptionInterface
     */
    public function sendTelegramMessage(string $text): void
    {
        $response = $this->client->request(
            'POST',
            $this->telegramUrl,
            [
                'body' => [
                    'token' => $this->token,
                    'chatId' => $this->telegramChatId,
                    'message' => $text,
                ]
            ]
        );
    }

    /**
     * @param string $text
     * @throws TransportExceptionInterface
     */
    public function sendTrueconfMessage(string $text): void
    {
        $response = $this->client->request(
            'POST',
            $this->trueconfUrl,
            [
                'body' => [
                    'token' => $this->token,
                    'chatId' => $this->trueconfChatId,
                    'message' => $text,
                ]
            ]
        );
    }
}