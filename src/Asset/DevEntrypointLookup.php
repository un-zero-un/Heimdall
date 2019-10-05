<?php

declare(strict_types=1);

namespace App\Asset;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookup;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;
use Symfony\WebpackEncoreBundle\Asset\IntegrityDataProviderInterface;

class DevEntrypointLookup implements EntrypointLookupInterface, IntegrityDataProviderInterface
{
    /**
     * @var EntrypointLookup
     */
    private $decorated;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(EntrypointLookup $decorated, RequestStack $requestStack)
    {
        $this->decorated    = $decorated;
        $this->requestStack = $requestStack;
    }

    public function getJavaScriptFiles(string $entryName): array
    {
        return array_map(
            function (string $file) {
                return str_replace('https://0.0.0.0:', 'https://' . $this->requestStack->getMasterRequest()->getHost() . ':', $file);
            },
            $this->decorated->getJavaScriptFiles($entryName)
        );
    }

    public function getCssFiles(string $entryName): array
    {
        return array_map(
            function (string $file) {
                return str_replace('https://0.0.0.0:', 'https://' . $this->requestStack->getMasterRequest()->getHost() . ':', $file);
            },
            $this->decorated->getCssFiles($entryName)
        );
    }

    public function reset()
    {
        return $this->decorated->reset();
    }

    public function getIntegrityData(): array
    {
        return $this->decorated->getIntegrityData();
    }
}
