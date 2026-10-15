<?php

namespace App\Services;

use App\Entity\Dto\Xml\SocialListContentDto;
use App\Repository\RknResponseRepository;
use DomainException;

/**
 * Class RknSocialListApiService
 * @package App\Services
 */
class RknSocialListApiService
{
    /**
     * @param RknResponseRepository $rknResponseRepository
     */
    public function __construct(
        private RknResponseRepository $rknResponseRepository
    )
    {
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getSocialListFormatted(): array
    {
        $rknResponse = $this->rknResponseRepository->getActualSocialList();

        if ($rknResponse) {
            $contentDtos = $rknResponse->getContentDtos();
            $formattedList = [];
            /** @var SocialListContentDto $contentDto */
            foreach ($contentDtos as $contentDto) {
                $formattedList[] = [
                    'name' => $contentDto->getResourceName(),
                    'domains' => $contentDto->getDomain(),
                ];
            }

            return [
                'update_time' => $rknResponse->getUpdateTime(),
                'social_list' => $formattedList
            ];
        }

        throw new DomainException('Не найден список социально значимых ресурсов');
    }
}