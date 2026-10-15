<?php

namespace App\Services;

use App\Event\LastDumpDateResponseEvent;
use App\Event\NoUpdateSocialListEvent;
use App\Event\StartUpdateSocialListEvent;
use App\Message\GetSocialListMessage;
use App\Repository\RknResponseRepository;
use App\RequestServices\ElectronicSignatureCreator;
use App\RequestServices\RequestContentCreator;
use DateTime;
use DateTimeInterface;
use DomainException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Получение соц. значимых ресурсов с помощью ЭП
 * Class RknSocialListService
 * @package App\Services
 */
class RknSocialListService
{

    public function __construct(
        private RknService                 $rknService,
        private ElectronicSignatureCreator $electronicSignatureCreator,
        private RequestContentCreator      $requestContentCreator,
        private MessageBusInterface        $bus,
        private RknResponseRepository      $rknResponseRepository,
        private EventDispatcherInterface   $eventDispatcher
    )
    {
    }

    /**
     *
     */
    public function syncSocialList(): void
    {
        $lastUpdateTimeListSocialRkn = $this->getLastUpdateTimeListSocialRkn();
        $actualUpdateTimeListSocialRkn = $this->getActualUpdateTimeListSocialRkn();

        if (!$actualUpdateTimeListSocialRkn or $actualUpdateTimeListSocialRkn < $lastUpdateTimeListSocialRkn) {
            $this->eventDispatcher->dispatch(new StartUpdateSocialListEvent());
            $this->updateSocialList();
        } else {
            $this->eventDispatcher->dispatch(new NoUpdateSocialListEvent());
        }
    }

    /**
     *
     */
    private function updateSocialList(): void
    {
        $xml = $this->requestContentCreator->create();
        $signatureXml = $this->electronicSignatureCreator->create($xml);

        $xmlFile = new UploadedFile($xml, 'xml');
        $signatureXmlFile = new UploadedFile($signatureXml, 'signatureXml');

        $result = $this->rknService->sendRequest($xmlFile, $signatureXmlFile);

        if ($result['result']) {
            $this->bus->dispatch(new GetSocialListMessage($result['code']));
        } else {
            throw new DomainException('Rkn: ' . $result['resultComment']);
        }
    }

    /**
     * @return DateTime
     */
    private function getLastUpdateTimeListSocialRkn(): DateTime
    {
        $lastSocDate = $this->rknService->getLastSocDate();
        $this->eventDispatcher->dispatch(new LastDumpDateResponseEvent($lastSocDate));
        $lastDumpDateSocResources = $lastSocDate['lastDumpDateSocResources']; // Время обновления выгрузки в миллисекундах
        // Время последнего обновления выгрузки соц. значимых ресурсов
        return (new DateTime())->setTimestamp($lastDumpDateSocResources / 1000);
    }

    /**
     * @return DateTimeInterface|null
     */
    private function getActualUpdateTimeListSocialRkn(): ?DateTimeInterface
    {
        return $this->rknResponseRepository->getActualSocialList()?->getUpdateTime();
    }
}