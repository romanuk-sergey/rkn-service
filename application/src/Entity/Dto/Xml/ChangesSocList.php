<?php

namespace App\Entity\Dto\Xml;

/**
 * Class ChangesSocList
 * @package App\Entity\Dto\Xml
 */
class ChangesSocList
{
    private array $added;
    private array $removed;
    private array $changed;

    /**
     * @param array $added
     * @param array $removed
     * @param array $changed
     */
    public function __construct(array $added, array $removed, array $changed)
    {
        $this->added = $added;
        $this->removed = $removed;
        $this->changed = $changed;
    }

    /**
     * @return array
     */
    public function getAdded(): array
    {
        return $this->added;
    }

    /**
     * @return array
     */
    public function getRemoved(): array
    {
        return $this->removed;
    }

    /**
     * @return array
     */
    public function getChanged(): array
    {
        return $this->changed;
    }
}