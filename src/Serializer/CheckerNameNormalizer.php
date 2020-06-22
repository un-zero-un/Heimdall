<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Model\Site;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

class CheckerNameNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    private NormalizerInterface $decorated;

    public function __construct(NormalizerInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return $this->decorated->denormalize($data, $type, $format, $context);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $result = $this->decorated->normalize($object, $format, $context);
        if (isset($result['configuredChecks'])) {
            foreach ($result['configuredChecks'] as $i => $configuredCheck) {
                $result['configuredChecks'][$i]['check'] = ([$configuredCheck['check'], 'getName'])();
            }
        }
        if (isset($result['configuredCheck'])) {
            $result['configuredCheck']['check'] = ([$result['configuredCheck']['check'], 'getName'])();
        }

        return $result;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        $this->decorated->supportsDenormalization($data, $type, $format);
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function setSerializer(SerializerInterface $serializer)
    {
        $this->decorated->setSerializer($serializer);
    }
}
