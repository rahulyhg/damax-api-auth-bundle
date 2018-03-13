<?php

declare(strict_types=1);

namespace Damax\Bundle\ApiAuthBundle\Security;

use Damax\Bundle\ApiAuthBundle\Extractor\Extractor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * @see https://symfony.com/doc/current/_images/authentication-guard-methods.png
 */
abstract class AbstractAuthenticator extends AbstractGuardAuthenticator
{
    private $extractor;

    public function __construct(Extractor $extractor)
    {
        $this->extractor = $extractor;
    }

    public function supports(Request $request): bool
    {
        return null !== $this->extractor->extractKey($request);
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return $this->errorResponse(Response::HTTP_UNAUTHORIZED);
    }

    public function getCredentials(Request $request): string
    {
        return $this->extractor->extractKey($request);
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return $this->errorResponse(Response::HTTP_FORBIDDEN);
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }

    private function errorResponse(int $code): JsonResponse
    {
        return JsonResponse::create(['message' => Response::$statusTexts[$code]], $code);
    }
}