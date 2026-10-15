<?php

namespace App\Entity\Dto;

use Symfony\Component\Mime\Address;

/**
 * Class EmailDto
 * @package App\Entity\Dto
 */
class EmailDto
{
    private array $to;
    private array $carbonCopy;
    private array $blindCarbonCopy;
    private array $from;
    private ?string $context = null;

    /**
     * @param array $to
     */
    public function setTo(array $to): void
    {
        $this->to = $to;
    }

    /**
     * @param array $carbonCopy
     */
    public function setCarbonCopy(array $carbonCopy): void
    {
        $this->carbonCopy = $carbonCopy;
    }

    /**
     * @param array $blindCarbonCopy
     */
    public function setBlindCarbonCopy(array $blindCarbonCopy): void
    {
        $this->blindCarbonCopy = $blindCarbonCopy;
    }

    /**
     * @param array $from
     */
    public function setFrom(array $from): void
    {
        $this->from = $from;
    }

    /**
     * @param string|null $context
     */
    public function setContext(?string $context): void
    {
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [];

        $array['to'] = $this->to;
        $array['carbonCopy'] = $this->carbonCopy;
        $array['blindCarbonCopy'] = $this->blindCarbonCopy;
        $array['from'] = $this->from;

        if ($this->context) {
            $array['context'] = $this->context;
        }

        return $array;
    }

    /**
     * @return false|string
     */
    public function toJson(): bool|string
    {
        return json_encode($this->toArray());
    }
}