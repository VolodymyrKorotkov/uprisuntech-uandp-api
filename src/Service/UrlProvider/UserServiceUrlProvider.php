<?php declare(strict_types=1);

namespace App\Service\UrlProvider;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class UserServiceUrlProvider
{
    public function __construct(
        #[Autowire(env: 'APP_URL')] private string $hostname
    )
    {
    }

    public function getGoogleLoginUrl(): string
    {
        return $this->hostname . '/oauth/google/login?siteAlias=';
    }

    public function getGovIdUaLoginUrl(): string
    {
        return $this->hostname . '/oauth/id-gov-ua/login';
    }
}
