<?php

namespace App\Entity\Dto;


class RequestData
{
    private string $requestTime;

    public function __construct(
        private string $operatorName,
        private string $inn,
        private string $ogrn,
        private string $email
    ) {
    }

    public function setRequestTime(string $requestTime): void
    {
        $this->requestTime = $requestTime;
    }

    public function getRequestTime(): string
    {
        return $this->requestTime;
    }

    public function getOperatorName(): ?string
    {
        return $this->operatorName;
    }

    public function getInn(): ?string
    {
        return $this->inn;
    }

    public function getOgrn(): ?string
    {
        return $this->ogrn;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}
