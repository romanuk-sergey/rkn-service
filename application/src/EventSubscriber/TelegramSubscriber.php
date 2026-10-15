<?php

namespace App\EventSubscriber;

use App\Event\ExceptionGetSocialListEvent;
use App\Event\NoResponseGetSocialListEvent;
use App\Event\NoUpdateSocialListEvent;
use App\Event\StartUpdateSocialListEvent;
use App\Event\SuccessfulGetSocialListNotificationEvent;
use App\Services\Social\TelegramService;
use App\Services\TelegramMessageGenerator;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TelegramSubscriber implements EventSubscriberInterface
{
    /**
     * @param TelegramService $telegramService
     * @param TelegramMessageGenerator $telegramMessageGenerator
     */
    public function __construct(
        private TelegramService          $telegramService,
        private TelegramMessageGenerator $telegramMessageGenerator,
    )
    {
    }

    public function onExceptionGetSocialList(ExceptionGetSocialListEvent $event): void
    {
        $resultCode = $event->getResultCode();
        $resultComment = $event->getResultComment();

        $this->telegramService->sendMessage('Ошибка: ' . $resultCode . ' - ' . $resultComment . '. ' . $this->getData());
    }

    public function onSuccessfulGetSocialList(SuccessfulGetSocialListNotificationEvent $event): void
    {
        $changesSocList = $event->getChangesSocList();

        $messagesAdd = $this->telegramMessageGenerator->addedMessage($changesSocList->getAdded());
        $messagesRemove = $this->telegramMessageGenerator->removedMessage($changesSocList->getRemoved());
        $messagesDiff = $this->telegramMessageGenerator->diffMessage($changesSocList->getChanged());

        $messages = array_merge($messagesAdd, $messagesRemove, $messagesDiff);

        foreach ($messages as $message) {
            $this->telegramService->sendMessage($message);
        }
    }

    public function onNoResponseGetSocialList(NoResponseGetSocialListEvent $event): void
    {
        $this->telegramService->sendMessage('Нет ответа от РКН. ' . $this->getData());
    }

    public function onStartUpdateSocialList(StartUpdateSocialListEvent $event): void
    {
        $this->telegramService->sendMessage('Началось формирование нового списка. ' . $this->getData());
    }

    public function onNoUpdateSocialList(NoUpdateSocialListEvent $event): void
    {
        $this->telegramService->sendMessage('Запрос прошёл успешно. Обновлений нет. ' . $this->getData());
    }

    public static function getSubscribedEvents()
    {
        return [
            ExceptionGetSocialListEvent::class => 'onExceptionGetSocialList',
            SuccessfulGetSocialListNotificationEvent::class => 'onSuccessfulGetSocialList',
            NoResponseGetSocialListEvent::class => 'onNoResponseGetSocialList',
            StartUpdateSocialListEvent::class => 'onStartUpdateSocialList',
            NoUpdateSocialListEvent::class => 'onNoUpdateSocialList',
        ];
    }

    /**
     * @return string
     */
    private function getData()
    {
        return (new DateTime())->format('Y-m-d H:i');
    }
}
