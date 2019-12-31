<?php

declare(strict_types=1);

namespace App\Controller\Login;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GoogleController
{
    private ClientRegistry $clientRegistry;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(ClientRegistry $clientRegistry, UrlGeneratorInterface $urlGenerator)
    {
        $this->clientRegistry = $clientRegistry;
        $this->urlGenerator   = $urlGenerator;
    }

    /**
     * @Route("/api/connect/google", name="connect_google_start")
     */
    public function connectAction(): RedirectResponse
    {
        return $this->clientRegistry
            ->getClient('google')
            ->redirect(['email'], []);
    }
}
