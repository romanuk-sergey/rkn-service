<?php

namespace App\Entity\Dto\Xml;

use DateTimeInterface;

/**
 * Class DiffSocialListContent
 * @package App\Entity\Dto\Xml
 */
class DiffSocialListContent
{
    private string $id;
    private string $domain;
    private ?string $oldHash = null;
    private ?string $newHash = null;
    private ?DateTimeInterface $oldIncludeTime = null;
    private ?DateTimeInterface $newIncludeTime = null;
    private string $oldResourceName;
    private ?string $newResourceName = null;
    private ?array $addedDomain = null;
    private ?array $removedDomain = null;
    private ?array $addedIpSubnet = null;
    private ?array $removedIpSubnet = null;

    /**
     * @param string $id
     * @param string $resourceName
     * @param string $domain
     */
    public function __construct(string $id, string $resourceName, string $domain)
    {
        $this->id = $id;
        $this->oldResourceName = $resourceName;
        $this->domain = $domain;
    }

    /**
     * @param string $oldHash
     */
    public function setOldHash(string $oldHash): void
    {
        $this->oldHash = $oldHash;
    }

    /**
     * @param string $newHash
     */
    public function setNewHash(string $newHash): void
    {
        $this->newHash = $newHash;
    }

    /**
     * @param DateTimeInterface $oldIncludeTime
     */
    public function setOldIncludeTime(DateTimeInterface $oldIncludeTime): void
    {
        $this->oldIncludeTime = $oldIncludeTime;
    }

    /**
     * @param DateTimeInterface $newIncludeTime
     */
    public function setNewIncludeTime(DateTimeInterface $newIncludeTime): void
    {
        $this->newIncludeTime = $newIncludeTime;
    }

    /**
     * @param string $newResourceName
     */
    public function setNewResourceName(string $newResourceName): void
    {
        $this->newResourceName = $newResourceName;
    }

    /**
     * @param array $addedDomain
     */
    public function setAddedDomain(array $addedDomain): void
    {
        $this->addedDomain = $addedDomain;
    }

    /**
     * @param array $removedDomain
     */
    public function setRemovedDomain(array $removedDomain): void
    {
        $this->removedDomain = $removedDomain;
    }

    /**
     * @param array $addedIpSubnet
     */
    public function setAddedIpSubnet(array $addedIpSubnet): void
    {
        $this->addedIpSubnet = $addedIpSubnet;
    }

    /**
     * @param array $removedIpSubnet
     */
    public function setRemovedIpSubnet(array $removedIpSubnet): void
    {
        $this->removedIpSubnet = $removedIpSubnet;
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
    public function getOldHash(): ?string
    {
        return $this->oldHash;
    }

    /**
     * @return string|null
     */
    public function getNewHash(): ?string
    {
        return $this->newHash;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getOldIncludeTime(): ?DateTimeInterface
    {
        return $this->oldIncludeTime;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getNewIncludeTime(): ?DateTimeInterface
    {
        return $this->newIncludeTime;
    }

    /**
     * @return string
     */
    public function getOldResourceName(): string
    {
        return $this->oldResourceName;
    }

    /**
     * @return string|null
     */
    public function getNewResourceName(): ?string
    {
        return $this->newResourceName;
    }

    /**
     * @return array|null
     */
    public function getAddedDomain(): ?array
    {
        return $this->addedDomain;
    }

    /**
     * @return array|null
     */
    public function getRemovedDomain(): ?array
    {
        return $this->removedDomain;
    }

    /**
     * @return array|null
     */
    public function getAddedIpSubnet(): ?array
    {
        return $this->addedIpSubnet;
    }

    /**
     * @return array|null
     */
    public function getRemovedIpSubnet(): ?array
    {
        return $this->removedIpSubnet;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }
}