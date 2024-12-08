<?php declare(strict_types=1);

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Throwable;

abstract class AbstractOauthCodeAuthenticator extends AbstractAuthenticator
{
    private readonly UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @throws Throwable
     */
    final public function authenticate(Request $request): Passport
    {
        $code = $this->getCode($request);
        if (empty($code)){
            throw $this->createInvalidUserCodeException();
        }
        if (!is_string($code)){
            throw $this->createInvalidUserCodeException();
        }

        $user = $this->userRepository->findByCode($this->getCode($request));
        if (!$user) {
            throw $this->createInvalidUserCodeException();
        }

        $user->removeCode();
        $this->userRepository->save($user);

        return new SelfValidatingPassport(
            new UserBadge($user->getUserIdentifier())
        );
    }

    final protected function createInvalidUserCodeException(): Throwable
    {
        return new CustomUserMessageAuthenticationException('Invalid user code.');
    }

    protected abstract function getCode(Request $request): mixed;
}
