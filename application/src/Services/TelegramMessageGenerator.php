<?php

namespace App\Services;

use App\Entity\Dto\Xml\DiffSocialListContent;
use App\Entity\Dto\Xml\SocialListContentDto;
use DateTime;

/**
 * Class TelegramMessageGenerator
 * @package App\Services
 */
class TelegramMessageGenerator
{
    public function addedMessage(array $dtos): array
    {
        $messages = [];
        /** @var SocialListContentDto $dto */
        foreach ($dtos as $dto) {
            $messages[] = "Добавлен ресурс <i>" . $dto->getResourceName() . ' ' . $this->getData() . "</i>." .
                "\nДобавлен домен:\n<i>" . implode("\n", $dto->getDomain()) . "</i>" .
                "\nДобавлен ip адрес:\n<i>" . implode("\n", $dto->getIpSubnet()) . "</i>";
        }

        return $messages;
    }

    public function diffMessage(array $dtos): array
    {
        $messages = [];
        /** @var DiffSocialListContent $dto */
        foreach ($dtos as $dto) {
            if ($dto->getNewResourceName()) {
                $messages[] = "Ресурс <i>" . $dto->getOldResourceName() . "</i>, домен <i>" . $dto->getDomain() . "</i>, изменён:" .
                    "\nНа ресурс <i>" . $dto->getNewResourceName() . "</i>, домен <i>" . $dto->getDomain() . ' ' . $this->getData() . "</i>";
            }

            if ($dto->getAddedDomain()) {
                $messages[] = "Ресурс <i>" . $dto->getOldResourceName() . "</i>, домен <i>" . $dto->getDomain() . "</i>." .
                    "\nДобавлен домен:\n<i>" . implode("\n", $dto->getAddedDomain()) . ' ' . $this->getData() . "</i>";
            }

            if ($dto->getRemovedDomain()) {
                $messages[] = "Ресурс <i>" . $dto->getOldResourceName() . "</i>, домен <i>" . $dto->getDomain() . "</i>." .
                    "\nУдалён домен:\n<i>" . implode("\n", $dto->getRemovedDomain()) . ' ' . $this->getData() . "</i>";
            }

            if ($dto->getAddedIpSubnet()) {
                $messages[] = "Ресурс <i>" . $dto->getOldResourceName() . "</i>, домен <i>" . $dto->getDomain() . "</i>." .
                    "\nДобавлен ip адрес:\n<i>" . implode("\n", $dto->getAddedIpSubnet()) . ' ' . $this->getData() . "</i>";
            }

            if ($dto->getRemovedIpSubnet()) {
                $messages[] = "Ресурс <i>" . $dto->getOldResourceName() . "</i>, домен <i>" . $dto->getDomain() . "</i>." .
                    "\nУдалён ip адрес:\n<i>" . implode("\n", $dto->getRemovedIpSubnet()) . ' ' . $this->getData() . "</i>";
            }
        }

        return $messages;
    }

    public function removedMessage(array $dtos): array
    {
        $messages = [];
        /** @var SocialListContentDto $dto */
        foreach ($dtos as $dto) {
            $messages[] = "Удалён ресурс <i>" . $dto->getResourceName() . ' ' . $this->getData() . "</i>." .
                "\nУдалён домен:\n<i>" . implode("\n", $dto->getDomain()) . "</i>" .
                "\nУдалён ip адрес:\n<i>" . implode("\n", $dto->getIpSubnet()) . "</i>";
        }

        return $messages;
    }

    /**
     * @return string
     */
    private function getData()
    {
        return (new DateTime())->format('Y-m-d H:i');
    }
}