<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\User;
use App\Repository\UserRepository;
use Doctrine\ORM\NoResultException;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GoogleAuthenticator extends SocialAuthenticator
{
    private ClientRegistry $clientRegistry;

    private UrlGeneratorInterface $urlGenerator;

    private AuthenticationSuccessHandler $successHandler;

    private UserRepository $userRepository;

    public function __construct(
        ClientRegistry $clientRegistry,
        UrlGeneratorInterface $urlGenerator,
        AuthenticationSuccessHandler $successHandler,
        UserRepository $userRepository
    )
    {
        $this->clientRegistry = $clientRegistry;
        $this->urlGenerator   = $urlGenerator;
        $this->successHandler = $successHandler;
        $this->userRepository = $userRepository;
    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'api_connect_google_check';
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->clientRegistry->getClient('google'));
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            $this->urlGenerator->generate('connect_google_start'),
            Response::HTTP_TEMPORARY_REDIRECT,
        );
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var GoogleUser $googleUser */
        $googleUser = $this->clientRegistry
            ->getClient('google')
            ->fetchUserFromToken($credentials);

        try {
            $user = $this->userRepository->findOneByEmail($googleUser->getEmail());
        } catch (NoResultException $e) {
            $user = new User($googleUser->getEmail(), $googleUser->getName());
            $this->userRepository->create($user);
        }

        $user->touch();
        $this->userRepository->update($user);

        return $userProvider->loadUserByUsername($user->getUsername());
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(
            ['message' => strtr($exception->getMessageKey(), $exception->getMessageData())],
            Response::HTTP_FORBIDDEN
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return $this->successHandler->onAuthenticationSuccess($request, $token);
    }
}
