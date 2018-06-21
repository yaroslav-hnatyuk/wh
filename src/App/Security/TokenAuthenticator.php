<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public function getCredentials(Request $request)
    {
        // Checks if the credential header is provided
        $token = $request->headers->get('X-AUTH-TOKEN');
        if (!$token) {
            $token = $request->cookies->get('X-AUTH-TOKEN');
        }

        if (!$token) {
            return;
        }

        $token = base64_decode($token);

        // Parse the header or ignore it if the format is incorrect.
        if (false === strpos($token, ':')) {
            return;
        }
        list($username, $role, $secret) = explode(':', $token, 3);
        
        return array(
            'username' => $username,
            'role' => $role,
            'secret' => $secret,
        );
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials['username']);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $encoder = $this->encoderFactory->getEncoder($user);

        $passwordAndSalt = $user->getPassword();
        if (false === strpos($passwordAndSalt, '___')) {
            return false;
        }

        list($password, $salt) = explode('___', $passwordAndSalt);

        if (!empty($user->getRoles()) && $password && $salt) {
            return in_array($user->getRoles()[0], array('user', 'manager', 'admin'), true)
                && hash('sha256', $salt) === $credentials['secret']
                && hash('sha256', $user->getRoles()[0]) === $credentials['role'];
        }

        return false;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = array(
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        );

        return new JsonResponse($data, 403);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        if (!isMobile()) {
            return new RedirectResponse('/login');
        }

        $data = array(
            // you might translate this message
            'message' => 'Authentication Required',
        );

        return new JsonResponse($data, 401);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}