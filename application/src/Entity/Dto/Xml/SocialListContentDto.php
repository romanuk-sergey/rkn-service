<?php

namespace App\Entity\Dto\Xml;

use DateTimeInterface;

/**
 * Class SocialListContentDto
 * @package App\Entity\Dto\Xml
 */
class SocialListContentDto
{
    private string $id;
    private ?string $hash;
    private DateTimeInterface $includeTime;
    private string $resourceName;
    private array $domain;
    private array $ipSubnet;

    /**
     * @param string $id
     * @param ?string $hash
     * @param DateTimeInterface $includeTime
     * @param string $resourceName
     * @param array $domain
     * @param array $ipSubnet
     */
    public function __construct(string $id, ?string $hash, DateTimeInterface $includeTime, string $resourceName, array $domain, array $ipSubnet)
    {
        $this->id = $id;
        $this->hash = $hash;
        $this->includeTime = $includeTime;
        $this->resourceName = $resourceName;
        $this->domain = $domain;
        $this->ipSubnet = $ipSubnet;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getHash(): ?string
    {
        return $this->hash;
    }

    /**
     * @return DateTimeInterface
     */
    public function getIncludeTime(): DateTimeInterface
    {
        return $this->includeTime;
    }

    /**
     * @return string
     */
    public function getResourceName(): string
    {
        return $this->resourceName;
    }

    /**
     * @return array
     */
    public function getDomain(): array
    {
        return $this->domain;
    }

    /**
     * @return array
     */
    public function getIpSubnet(): array
    {
        return $this->ipSubnet;
    }
}