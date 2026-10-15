<?php

namespace App\Services;

use App\Entity\Dto\Xml\ChangesSocList;
use App\Entity\Dto\Xml\DiffSocialListContent;
use App\Entity\Dto\Xml\SocialListContentDto;
use App\Entity\RknResponse;

/**
 * Class RknResponseCompareService
 * @package App\Services
 */
class RknResponseCompareService
{
    /**
     * @param RknResponse $responseOriginal
     * @param RknResponse $responseComparable
     * @return ChangesSocList
     */
    public function compare(RknResponse $responseOriginal, RknResponse $responseComparable): ChangesSocList
    {
        $arrayDiffSocialListContent = []; // Массив с dto-шками в которых содержатся изменения между ресурсами

        $responseOriginalContentDtos = $responseOriginal->getContentDtos();
        $responseComparableContentDtos = $responseComparable->getContentDtos();

        /** @var SocialListContentDto $contentDtoOriginal */
        foreach ($responseOriginalContentDtos as $originalIndex => $contentDtoOriginal) {
            /** @var SocialListContentDto $contentDtoComparable */
            foreach ($responseComparableContentDtos as $comparableIndex => $contentDtoComparable) {

                if ($contentDtoOriginal->getId() === $contentDtoComparable->getId()) { // найдены ресурсы с одинаковым id
                    // проверяем изменился ли ресурс
                    if ($this->isContentChanged($contentDtoOriginal, $contentDtoComparable)) {
                        // сравниваем поля ресурсов
                        $arrayDiffSocialListContent[] = $this->compareFields($contentDtoOriginal, $contentDtoComparable);
                    }

                    // удаляем из массивов контент, который уже сравнили
                    // если в этих массивах остался контент, значит это либо новый, либо удалился
                    unset($responseOriginalContentDtos[$originalIndex], $responseComparableContentDtos[$comparableIndex]);
                }
            }
        }

        return new ChangesSocList(
            $responseOriginalContentDtos,
            $responseComparableContentDtos,
            $arrayDiffSocialListContent
        );
    }

    /**
     * @param SocialListContentDto $contentDtoOriginal
     * @param SocialListContentDto $contentDtoComparable
     * @return bool
     */
    private function isContentChanged(SocialListContentDto $contentDtoOriginal, SocialListContentDto $contentDtoComparable): bool
    {
        return !$contentDtoOriginal->getHash() or !$contentDtoComparable->getHash() or $contentDtoOriginal->getHash() !== $contentDtoComparable->getHash();
    }

    /**
     * @param SocialListContentDto $contentDtoOriginal
     * @param SocialListContentDto $contentDtoComparable
     * @return DiffSocialListContent
     */
    private function compareFields(
        SocialListContentDto $contentDtoOriginal,
        SocialListContentDto $contentDtoComparable
    ): DiffSocialListContent
    {
        $diff = $this->createDiffContent($contentDtoComparable);

        if ($contentDtoOriginal->getResourceName() !== $contentDtoComparable->getResourceName()) {
            // в diff старое название ресурса уже есть
            $diff->setNewResourceName($contentDtoOriginal->getResourceName());
        }

        if ($contentDtoOriginal->getIncludeTime()->getTimestamp() !== $contentDtoComparable->getIncludeTime()->getTimestamp()) {
            $diff->setOldIncludeTime($contentDtoOriginal->getIncludeTime());
            $diff->setNewIncludeTime($contentDtoComparable->getIncludeTime());
        }

        if ($addedIpSubnets = array_diff($contentDtoOriginal->getIpSubnet(), $contentDtoComparable->getIpSubnet())) {
            $diff->setAddedIpSubnet($addedIpSubnets);
        }

        if ($removedIpSubnets = array_diff($contentDtoComparable->getIpSubnet(), $contentDtoOriginal->getIpSubnet())) {
            $diff->setRemovedIpSubnet($removedIpSubnets);
        }

        if ($addedDomain = array_diff($contentDtoOriginal->getDomain(), $contentDtoComparable->getDomain())) {
            $diff->setAddedDomain($addedDomain);
        }

        if ($removedDomain = array_diff($contentDtoComparable->getDomain(), $contentDtoOriginal->getDomain())) {
            $diff->setRemovedDomain($removedDomain);
        }

        return $diff;
    }

    /**
     * @param SocialListContentDto $contentDto
     * @return DiffSocialListContent
     */
    private function createDiffContent(SocialListContentDto $contentDto): DiffSocialListContent
    {
        return new DiffSocialListContent(
            $contentDto->getId(),
            $contentDto->getResourceName(),
            $contentDto->getDomain()[0]
        );
    }
}