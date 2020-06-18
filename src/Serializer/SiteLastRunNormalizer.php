<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Model\Site;
use App\Repository\RunRepository;
use Doctrine\ORM\UnexpectedResultException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

class SiteLastRunNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    private NormalizerInterface $decorated;

    private RunRepository $runRepository;

    public function __construct(NormalizerInterface $decorated, RunRepository $runRepository)
    {
        if (!$decorated instanceof DenormalizerInterface) {
            throw new \InvalidArgumentException(
                sprintf('The decorated normalizer must implement the %s.', DenormalizerInterface::class)
            );
        }

        $this->decorated     = $decorated;
        $this->runRepository = $runRepository;
    }

    public function supportsNormalization($data, string $format = null)
    {
        if ($data instanceof Site) {
            return true;
        }

        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        if (!$object instanceof Site) {
            return $this->decorated->normalize($object, $format, $context);
        }

        $normalized = $this->decorated->normalize($object, $format, $context);

        try {
            $normalized['lastRun'] = $this->decorated->normalize(
                $this->runRepository->findLastForSite($object),
                $format,
                $context
            );
        } catch (UnexpectedResultException $e) {
            $normalized['lastRun'] = null;
        }

        return $normalized;
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        return $this->decorated->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return $this->decorated->supportsDenormalization($data, $type, $format);
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }

}
