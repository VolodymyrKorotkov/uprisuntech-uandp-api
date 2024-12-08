<?php declare(strict_types=1);

namespace App\Service\OauthCallbackHandler\Dto;

final readonly class HandleOauthCallbackResult
{
    public function __construct(
        public string $redirectUrl
    )
    {
    }
}
