<?php

namespace App\Entity;

use App\Entity\Dto\Xml\SocialListContentDto;
use App\Repository\RknResponseRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use SimpleXMLElement;

/**
 * @ORM\Entity(repositoryClass=RknResponseRepository::class)
 */
class RknResponse
{
    use TimestampableEntity;

    public const TYPE_SOCIAL_DATE = 'social-date';
    public const TYPE_SOCIAL_LIST = 'social-list';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="text")
     */
    private $rawData;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /** @var array */
    protected $contentDtos;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updateTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRawData(): ?string
    {
        return $this->rawData;
    }

    public function setRawData(string $rawData): self
    {
        $this->rawData = $rawData;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'updateTime' => $this->updateTime->format('Y-m-d H:i:s'),
        ];
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    public function setUpdateTime(\DateTimeInterface $updateTime): self
    {
        $this->updateTime = $updateTime;

        return $this;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getContentDtos(): array
    {
        $xml = new SimpleXMLElement($this->rawData, LIBXML_NOCDATA);

        $arrayContentDtos = [];

        foreach ($xml->content as $content) {
            $dateTime = new DateTime($content['includeTime']);

            $item = new SocialListContentDto(
                $content['id'],
                $content['hash'] ?? null,
                $dateTime,
                $content->resourceName,
                (array)$content->domain,
                (array)$content->ipSubnet,
            );

            $arrayContentDtos[] = $item;
        }

        return $arrayContentDtos;
    }
}
