<?php

namespace App\RequestServices;

use App\Entity\PrivateKey;
use App\Repository\PrivateKeyRepository;
use DomainException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ElectronicSignatureCreator
{
    private ?PrivateKey $privateKey;
    private string $pathPrivateKey;
    private string $passPrivateKey;

    /**
     * @param ParameterBagInterface $parameterBag
     * @param PrivateKeyRepository $privateKeyRepository
     */
    public function __construct(
        private ParameterBagInterface $parameterBag,
        private PrivateKeyRepository  $privateKeyRepository,
    )
    {
        $privateKey = $this->privateKeyRepository->findOneBy([]);
        $this->privateKey = $privateKey;
        if ($privateKey) {
            $this->pathPrivateKey = $privateKey->getPath();
            $this->passPrivateKey = $privateKey->getPassword();
        }
    }

    public function create(string $pathRequest): string
    {
        if (!$this->privateKey) {
            throw new DomainException('Электронная подпись не найдена');
        }

        $pathSign = $this->parameterBag->get('secret.directory') . '/request/signed.txt';

        // рабочий вариант
        exec("openssl smime -sign -signer " . $this->pathPrivateKey . " -passin pass:" . $this->passPrivateKey . " -engine gost -nodetach -binary -noattr -outform DER -in " . $pathRequest . " -out " . $pathSign);

        return $pathSign;
    }
}