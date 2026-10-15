<?php

namespace App\Entity\Dto;

/**
 * Class ApiSuccessResponseDto
 * @package App\Entity\Dto
 */
class ApiSuccessResponseDto
{

    /**
     * @param $data
     */
    public function __construct(
        private $data
    )
    {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'status' => 200,
            'error' => '',
            'data' => $this->data,
        ];
    }
}