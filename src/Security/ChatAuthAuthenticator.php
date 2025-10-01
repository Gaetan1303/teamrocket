<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use App\Repository\SbireRepository;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ChatAuthAuthenticator extends AbstractAuthenticator
{
    public function __construct(private SbireRepository $sbireRepository)
    {
    }

    public function supports(Request $request): ?bool
    {
        // On ne s'active que sur les routes chat
        return str_starts_with($request->getPathInfo(), '/chat');
    }

    public function authenticate(Request $request): Passport
    {
        $token = $request->headers->get('X-Chat-Token')
            ?? $request->query->get('chatToken');

        if (!$token) {
            throw new AuthenticationException('No chat token provided');
        }

        $codename = trim((string) $token);

        if ($codename === '') {
            throw new AuthenticationException('Invalid chat token');
        }

        // Attempt to load the Sbire by codename
        return new SelfValidatingPassport(
            new UserBadge($codename, function () use ($codename) {
                $sbire = $this->sbireRepository->findOneBy(['codename' => $codename]);

                if (!$sbire) {
                    throw new AuthenticationException('Unknown chat user');
                }

                return $sbire;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // On laisse passer (null = continue) car très utile 
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_UNAUTHORIZED);
    }
}