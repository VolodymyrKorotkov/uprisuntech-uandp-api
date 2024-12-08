<?php declare(strict_types=1);

namespace App\Service\FormIoClient;

use App\Service\HttpNativeClient\HttpNativeClient;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class FormIoSuperAdminClient extends AbstractFormioClient
{
    public function __construct(
        #[Autowire(env: 'FORMIO_PROJECT_URL')]
        private readonly string $formioClientUrl,
        #[Autowire(env: 'FORMIO_API_KEY')]
        private readonly string $apiSecret,
        HttpNativeClient        $httpNativeClient
    )
    {
        parent::__construct($httpNativeClient);
    }

    protected function getTokenHeader(): string
    {
        return self::X_TOKEN_HEADER;
    }

    protected function getToken(): string
    {
        return $this->apiSecret;
    }

    protected function getApiUrl(): string
    {
        return $this->formioClientUrl;
    }

    protected function hasToken(): bool
    {
        return true;
    }
}
