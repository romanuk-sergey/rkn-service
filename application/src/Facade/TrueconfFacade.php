<?php

namespace App\Facade;

use DomainException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class TrueconfFacade
 * @package App\Facade
 */
class TrueconfFacade
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
            $this->socialFacade->sendTrueconfMessage($text);
        } catch (TransportExceptionInterface $exception) {
            throw new DomainException('Не удалось отправить уведомление в Trueconf', 0, $exception);
        }

    }
}