<?php

namespace App\EventSubscriber;

use App\Event\ExceptionGetSocialListEvent;
use App\Event\NoResponseGetSocialListEvent;
use App\Event\NoUpdateSocialListEvent;
use App\Event\StartUpdateSocialListEvent;
use App\Event\SuccessfulGetSocialListNotificationEvent;
use App\Services\Social\TrueconfService;
use App\Services\TelegramMessageGenerator;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TrueconfSubscriber implements EventSubscriberInterface
{
    /**
     * @param TrueconfService $trueconfService
     * @param TelegramMessageGenerator $telegramMessageGenerator
     */
    public function __construct(
        private TrueconfService          $trueconfService,
        // Так как пока что нет надобности в изменении сообщений - оставлен оригинальный генератор дабы не плодить дубли
        private TelegramMessageGenerator $telegramMessageGenerator,
    )
    {
    }

    public function onExceptionGetSocialList(ExceptionGetSocialListEvent $event): void
    {
        $resultCode = $event->getResultCode();
        $resultComment = $event->getResultComment();

        $this->trueconfService->sendMessage('Ошибка: ' . $resultCode . ' - ' . $resultComment . '. ' . $this->getData());
    }

    public function onSuccessfulGetSocialList(SuccessfulGetSocialListNotificationEvent $event): void
    {
        $changesSocList = $event->getChangesSocList();

        $messagesAdd = $this->telegramMessageGenerator->addedMessage($changesSocList->getAdded());
        $messagesRemove = $this->telegramMessageGenerator->removedMessage($changesSocList->getRemoved());
        $messagesDiff = $this->telegramMessageGenerator->diffMessage($changesSocList->getChanged());

        $messages = array_merge($messagesAdd, $messagesRemove, $messagesDiff);

        foreach ($messages as $message) {
            $this->trueconfService->sendMessage($message);
        }
    }

    public function onNoResponseGetSocialList(NoResponseGetSocialListEvent $event): void
    {
        $this->trueconfService->sendMessage('Нет ответа от РКН. ' . $this->getData());
    }

    public function onStartUpdateSocialList(StartUpdateSocialListEvent $event): void
    {
        $this->trueconfService->sendMessage('Началось формирование нового списка. ' . $this->getData());
    }

    public function onNoUpdateSocialList(NoUpdateSocialListEvent $event): void
    {
        $this->trueconfService->sendMessage('Запрос прошёл успешно. Обновлений нет. ' . $this->getData());
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
