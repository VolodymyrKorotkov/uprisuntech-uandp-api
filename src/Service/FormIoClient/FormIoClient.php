<?php

namespace App\Service\FormIoClient;

use App\Service\HttpNativeClient\HttpNativeClient;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Throwable;

final class FormIoClient extends AbstractFormioClient
{
    private string|null $jwtToken = null;

    public function __construct(
        #[Autowire(env: 'FORMIO_PROJECT_URL')]
        private readonly string                   $formioClientUrl,
        private readonly AuthUserJwtTokenProvider $tokenProvider,
        HttpNativeClient                          $httpNativeClient,
        private readonly Security                 $security,
    )
    {
        parent::__construct($httpNativeClient);
    }

    protected function getTokenHeader(): string
    {
        return self::X_JWT_TOKEN_HEADER;
    }

    protected function hasToken(): bool
    {
        return null !== $this->security->getUser();
    }

    /**
     * @throws Throwable
     */
    protected function getToken(): string
    {
        if (null === $this->jwtToken) {
            $this->jwtToken = $this->tokenProvider->getJwtToken();
        }

        return $this->jwtToken;
    }

    protected function getApiUrl(): string
    {
        return $this->formioClientUrl;
    }
}
