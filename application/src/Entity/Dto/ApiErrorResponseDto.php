<?php

namespace App\Entity\Dto;

/**
 * Class ApiErrorResponseDto
 * @package App\Entity\Dto
 */
class ApiErrorResponseDto
{

    /**
     * @param string $error
     * @param int $status
     * @param string $data
     */
    public function __construct(
        private string $error,
        private int    $status = 500,
        private string $data = '',
    )
    {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'error' => $this->error,
            'data' => $this->data,
        ];
    }
}