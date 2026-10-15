<?php

namespace App\EventSubscriber;

use App\Entity\Log;
use App\Event\CommandGetSocialListEvent;
use App\Event\ManualGetSocialListEvent;
use App\Event\SendEmailEvent;
use App\Event\SendTelegramMessageEvent;
use App\Event\SendTrueconfMessageEvent;
use App\Event\SocialResourceResponseEvent;
use App\Services\Converters\EmailConverter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EmailConverter         $emailConverter,
    )
    {
    }

    public function onSocialResourceResponseEvent(SocialResourceResponseEvent $event): void
    {
        $log = new Log();
        $log->setType(Log::UPDATE_LIST_TYPE);
        $response = $event->getResponse();

        $registerZipArchive = $response['registerZipArchive'];
        $response['registerZipArchive'] = base64_encode($registerZipArchive);

        $log->setData(json_encode($response, JSON_PARTIAL_OUTPUT_ON_ERROR));

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    public function onCommandGetSocialListEvent(): void
    {
        $log = new Log();
        $log->setType(Log::AUTO_QUERY_TYPE);

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    public function onManualGetSocialListEvent(): void
    {
        $log = new Log();
        $log->setType(Log::MANUAL_QUERY_TYPE);

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    public function onSendEmailEvent(SendEmailEvent $event): void
    {
        $log = new Log();
        $log->setType(Log::SEND_EMAIL_TYPE);

        $email = $event->getEmail();

        $emailDto = $this->emailConverter->toEmailDto($email);

        $log->setData($emailDto->toJson());

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    public function onSendTelegramMessageEvent(SendTelegramMessageEvent $event): void
    {
        $log = new Log();
        $log->setType(Log::SEND_TELEGRAM_TYPE);

        $text = $event->getText();

        $log->setData(json_encode($text));
        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    public function onSendTrueconfMessageEvent(SendTrueconfMessageEvent $event): void
    {
        $log = new Log();
        $log->setType(Log::SEND_TRUECONF_TYPE);

        $text = $event->getText();

        $log->setData(json_encode($text));
        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    public static function getSubscribedEvents()
    {
        return [
            SocialResourceResponseEvent::class => 'onSocialResourceResponseEvent',
            CommandGetSocialListEvent::class => 'onCommandGetSocialListEvent',
            ManualGetSocialListEvent::class => 'onManualGetSocialListEvent',
            SendEmailEvent::class => 'onSendEmailEvent',
            SendTelegramMessageEvent::class => 'onSendTelegramMessageEvent',
            SendTrueconfMessageEvent::class => 'onSendTrueconfMessageEvent',
        ];
    }
}
