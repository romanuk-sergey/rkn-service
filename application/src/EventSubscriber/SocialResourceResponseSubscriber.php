<?php

namespace App\EventSubscriber;

use App\Entity\RknResponse;
use App\Event\SocialResourceResponseEvent;
use App\Event\SuccessfulGetSocialListNotificationEvent;
use App\Repository\RknResponseRepository;
use App\Services\RknResponseCompareService;
use App\Services\TelegramMessageGenerator;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use SimpleXMLElement;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Throwable;
use ZipArchive;

/**
 * Class SocialResourceResponseSubscriber
 * @package App\EventSubscriber
 */
class SocialResourceResponseSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private EntityManagerInterface    $entityManager,
        private EventDispatcherInterface  $eventDispatcher,
        private RknResponseRepository     $rknResponseRepository,
        private RknResponseCompareService $rknResponseCompareService,
        private TelegramMessageGenerator  $telegramMessageGenerator,
        private ParameterBagInterface     $parameterBag,
    )
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            SocialResourceResponseEvent::class => 'onSocialResourceResponseEvent',
        ];
    }

    /**
     * @param SocialResourceResponseEvent $event
     */
    public function onSocialResourceResponseEvent(SocialResourceResponseEvent $event): void
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor(); // array helper

        $array = $event->getResponse();

        $result = $propertyAccessor->getValue($array, '[result]');
        $resultCode = $propertyAccessor->getValue($array, '[resultCode]');
        $registerZipArchive = $propertyAccessor->getValue($array, '[registerZipArchive]');

        if ($result and $resultCode === 1) { // список готов
            $lastRknResponse = $this->rknResponseRepository->getActualSocialList();

            $xml = $this->getXml($registerZipArchive);

            // сохранение в базу
            $rknResponse = new RknResponse();
            $rknResponse->setType(RknResponse::TYPE_SOCIAL_LIST);
            $rknResponse->setRawData($xml);
            $rknResponse->setUpdateTime($this->getUpdateTime($xml));

            $this->entityManager->persist($rknResponse);
            $this->entityManager->flush();

            if ($lastRknResponse) { // проверка, на существование хотя бы одной записи в БД
                $this->notifyChanges($lastRknResponse, $rknResponse);
            }
        }
    }

    /**
     * @param $lastRknResponse
     * @param $rknResponse
     */
    private function notifyChanges($lastRknResponse, $rknResponse): void
    {
        $changesSocList = $this->rknResponseCompareService->compare($rknResponse, $lastRknResponse);
        $this->eventDispatcher->dispatch(new SuccessfulGetSocialListNotificationEvent($changesSocList));
    }

    /**
     * @param $registerZipArchive
     * @return false|string
     */
    private function getXml($registerZipArchive): bool|string
    {
        $zip = new ZipArchive();
        file_put_contents($this->parameterBag->get('kernel.project_dir') . '/public/archive.zip', $registerZipArchive);
        $zip->open($this->parameterBag->get('kernel.project_dir') . '/public/archive.zip');
        return $zip->getFromName('register.xml'); // register_test.xml для тестового сервера
    }

    /**
     * @param $xml
     * @return DateTime
     */
    private function getUpdateTime($xml): DateTime
    {
        try {
            $simpleXml = new SimpleXMLElement($xml);
        } catch (Throwable $exception) {
            throw new DomainException('Ошибка парсинга XML', 500, $exception);
        }

        $updateTime = $simpleXml['updateTime'];

        try {
            $date = new DateTime((string)$updateTime);
        } catch (Throwable $exception) {
            throw new DomainException('Ошибка получения даты обновления списка соц. значимых ресурсов', 500, $exception);
        }

        return $date;
    }
}