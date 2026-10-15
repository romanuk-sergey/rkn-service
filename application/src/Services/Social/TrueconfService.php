<?php

namespace App\Services\Social;

use App\Event\SendTrueconfMessageEvent;
use App\Facade\TrueconfFacade;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Throwable;

/**
 * Class TrueconfService
 * @package App\Services\Social
 */
class TrueconfService
{
    /**
     * @param TrueconfFacade $trueconfFacade
     * @param LoggerInterface $logger
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        private TrueconfFacade           $trueconfFacade,
        private LoggerInterface          $logger,
        private EventDispatcherInterface $eventDispatcher,
    )
    {
    }

    /**
     * @param $text
     */
    public function sendMessage($text)
    {
        $this->eventDispatcher->dispatch(new SendTrueconfMessageEvent($text));

        try {
            $this->trueconfFacade->send($text);
        } catch (Throwable $exception) {
            $this->logger->error('Ошибка отправки сообщения в Trueconf: ' . $exception->getMessage());
        }
    }
}