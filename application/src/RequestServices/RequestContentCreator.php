<?php

namespace App\RequestServices;

use App\Entity\Dto\RequestData;
use DateTime;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class RequestContentCreator
{
    private Serializer $serializer;

    public function __construct(
        private RequestData $requestData,
        private ParameterBagInterface $parameterBag,
    )
    {
        $this->requestData->setRequestTime((new DateTime())->format(DATE_ATOM));
        $metadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $converter = new MetadataAwareNameConverter($metadataFactory);
        $this->serializer = new Serializer([new ObjectNormalizer($metadataFactory, $converter)], [new XmlEncoder()]);
    }

    public function create(): string
    {
        $request = $this->serializer->encode($this->requestData, 'xml', [
            'xml_version' => '1.0',
            'xml_format_output' => true,
            'xml_encoding' => 'windows-1251',
            'xml_root_node_name' => 'request'
        ]);

        $path = $this->parameterBag->get('secret.directory') . '/request/content.xml';
        $filesystem = new Filesystem();
        $filesystem->dumpFile(
            $path,
            $request
        );
        return $path;
    }
}