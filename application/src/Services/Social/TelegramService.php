<?php

namespace App\Services\Social;

use App\Event\SendTelegramMessageEvent;
use App\Facade\TelegramFacade;
use DomainException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Throwable;

/**
 * Class TelegramService
 * @package App\Services\Social
 */
class TelegramService
{
    /**
     * @param TelegramFacade $telegramFacade
     * @param LoggerInterface $logger
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        private TelegramFacade           $telegramFacade,
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
        $this->eventDispatcher->dispatch(new SendTelegramMessageEvent($text));

        try {
            $this->telegramFacade->send($text);
        } catch (Throwable $exception) {
            $this->logger->error('Ошибка отправки сообщения в ТГ: ' . $exception->getMessage());
        }
    }
}