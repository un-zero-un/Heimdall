<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Model\RunCheckResult;
use App\Model\Site;
use App\Repository\RunCheckResultRepository;
use Doctrine\ORM\UnexpectedResultException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

class SiteLastResultsNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    /**
     * @var DenormalizerInterface&NormalizerInterface
     */
    private NormalizerInterface $decorated;

    private RunCheckResultRepository $runCheckResultRepository;

    public function __construct(NormalizerInterface $decorated, RunCheckResultRepository $runCheckResultRepository)
    {
        if (!$decorated instanceof DenormalizerInterface) {
            throw new \InvalidArgumentException(
                sprintf('The decorated normalizer must implement the %s.', DenormalizerInterface::class)
            );
        }

        $this->decorated                = $decorated;
        $this->runCheckResultRepository = $runCheckResultRepository;
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
        if (!is_array($normalized)) {
            return $normalized;
        }

        try {
            $normalized['lastResults'] = array_map(
                fn(RunCheckResult $result) => $this->decorated->normalize($result, $format, $context),
                $this->runCheckResultRepository->findLastOfEachCheckClassForSite($object),
            );
        } catch (UnexpectedResultException $e) {
            $normalized['lastResults'] = [];
        }

        return $normalized;
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        return $this->decorated->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization($data, string $type, string $format = null)
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
