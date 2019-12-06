<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Model\Site;
use App\ResultCompiler\SiteLastResultsCompiler;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

class SiteNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    private NormalizerInterface $decorated;

    private SiteLastResultsCompiler $lastResultsCompiler;

    public function __construct(SiteLastResultsCompiler $lastResultsCompiler, NormalizerInterface $decorated)
    {
        if (!$decorated instanceof DenormalizerInterface) {
            throw new \InvalidArgumentException(
                sprintf('The decorated normalizer must implement the %s.', DenormalizerInterface::class)
            );
        }

        $this->decorated           = $decorated;
        $this->lastResultsCompiler = $lastResultsCompiler;
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return $this->decorated->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $this->decorated->supportsDenormalization($data, $type, $format);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $normalized = $this->decorated->normalize($object, $format, $context);
        if ($object instanceof Site) {
            $normalized['lastLevelsGroupedByCheckers'] = $this->lastResultsCompiler->getLastLevelsGroupedByCheckers($object);
            $normalized['currentLowerResultLevel'] = $this->lastResultsCompiler->getCurrentLowerResultLevel($object);
        }

        return $normalized;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function setSerializer(SerializerInterface $serializer)
    {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }
}
