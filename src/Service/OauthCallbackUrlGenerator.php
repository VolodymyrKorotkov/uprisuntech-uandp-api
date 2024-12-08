<?php declare(strict_types=1);

namespace App\Service;

use App\Enum\AppRouteNameEnum;
use App\Enum\OauthTypeEnum;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class OauthCallbackUrlGenerator
{
    public function __construct(
        #[Autowire(env: 'APP_URL')] private string $hostname,
        private UrlGeneratorInterface                   $urlGenerator
    )
    {
    }

    public function generateCallbackUrl(OauthTypeEnum $oauthType): string
    {
        return $this->hostname.$this->urlGenerator->generate(
            name: AppRouteNameEnum::OAUTH_CALL_BACK_URL_NAME,
            parameters: [
                'oauthType' => $oauthType->value
            ]
        );
    }
}
