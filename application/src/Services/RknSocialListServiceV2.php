<?php

namespace App\Services;

use App\Event\LastDumpDateResponseEvent;
use App\Event\NoResponseGetSocialListEvent;
use App\Event\NoUpdateSocialListEvent;
use App\Event\SocialResourceResponseEvent;
use App\Repository\RknResponseRepository;
use DateTime;
use DateTimeInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

/**
 * Получение соц. значимых ресурсов с помощью логина и пароля
 * Class RknSocialListServiceV2
 * @package App\Services
 */
class RknSocialListServiceV2
{

    public function __construct(
        private RknServiceV2               $rknService,
        private RknResponseRepository      $rknResponseRepository,
        private EventDispatcherInterface   $eventDispatcher
    )
    {
    }

    /**
     * @return void
     */
    public function syncSocialList(): void
    {
        $lastUpdateTimeListSocialRkn = $this->getLastUpdateTimeListSocialRkn(); // время последнего обновления ресурсов в ркн
        $actualUpdateTimeListSocialRkn = $this->getActualUpdateTimeListSocialRkn(); // время последнего обновления ресурсов в базе

        if (!$actualUpdateTimeListSocialRkn or $actualUpdateTimeListSocialRkn < $lastUpdateTimeListSocialRkn) {
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
        try {
            $array = $this->rknService->getSocList();
            // 2 Обработчика этого события. 1 - запись в лог. 2 - запись в базу и отправка в ТГ
            $this->eventDispatcher->dispatch(new SocialResourceResponseEvent($array)); // обработка ответа и сохранение в базу
        } catch (TransportException $exception) {
            $this->eventDispatcher->dispatch(new NoResponseGetSocialListEvent());
            throw new UnrecoverableMessageHandlingException($exception->getMessage(), 500, $exception);
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
