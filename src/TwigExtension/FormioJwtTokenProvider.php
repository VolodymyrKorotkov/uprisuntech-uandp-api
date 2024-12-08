<?php declare(strict_types=1);

namespace App\TwigExtension;

use App\Security\ApplicationUserSecurity;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class FormioJwtTokenProvider extends AbstractExtension
{
    public function __construct(
        private ApplicationUserSecurity $applicationUserSecurity
    )
    {
    }

    public function getFunctions(): array|\Generator
    {
        yield new TwigFunction(
            name: 'getFormioJwtToken',
            callable: fn() => $this->applicationUserSecurity->isUserAuth() ?
                $this->applicationUserSecurity->getUser()->getLastJwtToken()->getJwtToken() :
                null
        );
    }
}
