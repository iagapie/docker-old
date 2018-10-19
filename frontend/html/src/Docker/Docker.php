<?php

declare(strict_types=1);

namespace App\Docker;

use App\Docker\API\Normalizer\NetworkSettingsNormalizer;
use Docker\DockerClientFactory;

class Docker extends \Docker\Docker
{
    public static function create($httpClient = null)
    {
        if (null === $httpClient) {
            $httpClient = DockerClientFactory::createFromEnv();
        }

        $messageFactory = \Http\Discovery\MessageFactoryDiscovery::find();
        $streamFactory = \Http\Discovery\StreamFactoryDiscovery::find();

        $normalizers = \Docker\API\Normalizer\NormalizerFactory::create();

        foreach ($normalizers as $i => $normalizer) {
            if ($normalizer instanceof \Docker\API\Normalizer\NetworkSettingsNormalizer) {
                unset($normalizers[$i]);
                break;
            }
        }

        $normalizers[] = new NetworkSettingsNormalizer();

        $serializer = new \Symfony\Component\Serializer\Serializer($normalizers, [new \Symfony\Component\Serializer\Encoder\JsonEncoder(new \Symfony\Component\Serializer\Encoder\JsonEncode(), new \Symfony\Component\Serializer\Encoder\JsonDecode())]);

        return new static($httpClient, $messageFactory, $serializer, $streamFactory);
    }
}
