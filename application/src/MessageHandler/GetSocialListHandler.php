<?php

namespace App\MessageHandler;

use App\Event\ExceptionGetSocialListEvent;
use App\Event\NoResponseGetSocialListEvent;
use App\Event\NotReadyGetSocialListEvent;
use App\Event\SocialResourceResponseEvent;
use App\Event\SuccessfulGetSocialListEvent;
use App\Message\GetSocialListMessage;
use App\Services\RknService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\RecoverableMessageHandlingException;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class GetSocialListHandler
 * @package App\MessageHandler
 */
#[AsMessageHandler]
class GetSocialListHandler
{

    public function __construct(
        private RknService               $rknService,
        private EventDispatcherInterface $eventDispatcher
    )
    {
    }

    public function __invoke(GetSocialListMessage $message)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $code = $message->getCode();

        try {
            $array = $this->rknService->getSocList($code);
            $this->eventDispatcher->dispatch(new SocialResourceResponseEvent($array));
        } catch (TransportException $exception) {
            $this->eventDispatcher->dispatch(new NoResponseGetSocialListEvent());
            throw new UnrecoverableMessageHandlingException($exception->getMessage(), 500, $exception);
        }

        $result = $propertyAccessor->getValue($array, '[result]');
        $resultCode = $propertyAccessor->getValue($array, '[resultCode]');
        $resultComment = $propertyAccessor->getValue($array, '[resultComment]') ?? '';

        if ($result and $resultCode === 1) {
            // Событие об успешном получении списка соц. значимых ресурсов
            $this->eventDispatcher->dispatch(new SuccessfulGetSocialListEvent($array));
            return;
        }

        if (!$result and $resultCode === 0) {
            // Список ещё не готов
            $this->eventDispatcher->dispatch(new NotReadyGetSocialListEvent($array));
            throw new RecoverableMessageHandlingException('rkn:запрос обрабатывается');
        }

        // Ошибка при получении списка
        $this->eventDispatcher->dispatch(new ExceptionGetSocialListEvent($resultComment, $resultCode));
        throw new UnrecoverableMessageHandlingException($resultComment, $resultCode);
    }
}