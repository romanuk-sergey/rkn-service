<?php

namespace App\Services\Converters;

use App\Entity\Dto\EmailDto;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class EmailConverter
 * @package App\Services\Converters
 */
class EmailConverter
{
    private Serializer $serializer;

    public function __construct()
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @param TemplatedEmail $email
     * @return EmailDto
     */
    public function toEmailDto(TemplatedEmail $email): EmailDto
    {
        $emailDto = new EmailDto();
        $emailDto->setTo($this->addressesToArrayString($email->getTo()));
        $emailDto->setCarbonCopy($this->addressesToArrayString($email->getCc()));
        $emailDto->setBlindCarbonCopy($this->addressesToArrayString($email->getBcc()));
        $emailDto->setFrom($this->addressesToArrayString($email->getFrom()));

        $context = $this->serializer->serialize($email->getContext(), 'json');

        $emailDto->setContext($context);

        return $emailDto;
    }

    /**
     * @param array|null $addresses
     * @return array
     */
    private function addressesToArrayString(?array $addresses): array
    {
        $array = [];

        foreach ($addresses as $address) {
            /** @var Address $address */
            $array[] = $address->getAddress();
        }

        return $array;
    }
}