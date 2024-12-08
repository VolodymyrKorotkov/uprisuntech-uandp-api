<?php declare(strict_types=1);

namespace App\Service\OauthGoogleProvider;

use Google\Service\Exception;
use Google\Service\Oauth2\Userinfo;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

#[AsDecorator(GoogleResourceOwnerProviderInterface::class)]
final readonly class UnAuthorizedExceptionDecorator implements GoogleResourceOwnerProviderInterface
{
    public function __construct(
        private GoogleResourceOwnerProviderInterface $resourceOwnerProvider
    )
    {
    }

    /**
     * @throws Exception
     */
    public function getResourceOwner(string $code): Userinfo
    {
        try {
            return $this->resourceOwnerProvider->getResourceOwner($code);
        } catch (\Google\Service\Exception $throwable){
            foreach ($throwable->getErrors() as $error){
                if ($error['reason'] === 'unauthorized'){
                    throw new AuthenticationException('Google code is invalid');
                }
            }

            throw $throwable;
        }
    }
}
