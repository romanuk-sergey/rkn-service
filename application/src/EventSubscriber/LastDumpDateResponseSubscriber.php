<?php

namespace App\EventSubscriber;

use App\Entity\RknResponse;
use App\Event\LastDumpDateResponseEvent;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LastDumpDateResponseSubscriber
 * @package App\EventSubscriber
 */
class LastDumpDateResponseSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            LastDumpDateResponseEvent::class => 'onLastDumpDateResponseEvent',
        ];
    }

    /**
     * @param LastDumpDateResponseEvent $event
     */
    public function onLastDumpDateResponseEvent(LastDumpDateResponseEvent $event): void
    {
        $array = $event->getResponse();

        // сохранение в базу
        $rknResponse = new RknResponse();
        $rknResponse->setType(RknResponse::TYPE_SOCIAL_DATE);
        $rknResponse->setRawData(json_encode($array));

        $rknResponse->setUpdateTime(new DateTime());

        $this->entityManager->persist($rknResponse);
        $this->entityManager->flush();
    }
}