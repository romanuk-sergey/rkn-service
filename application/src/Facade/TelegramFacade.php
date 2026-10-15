<?php

namespace App\Facade;

use DomainException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class TelegramFacade
 * @package App\Facade
 */
class TelegramFacade
{
    public function __construct(private SocialFacade $socialFacade)
    {
    }

    /**
     * @param string $text
     */
    public function send(string $text): void
    {
        try {
            $this->socialFacade->sendTelegramMessage($text);
        } catch (TransportExceptionInterface $exception) {
            throw new DomainException('Не удалось отправить уведомление в telegram', 0, $exception);
        }

    }
}