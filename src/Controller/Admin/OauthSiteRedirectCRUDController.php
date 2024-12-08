<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\SiteRedirect;
use App\Enum\UserRoleEnum;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[AsController]
#[IsGranted(UserRoleEnum::ROLE_SUPER_ADMIN_CASE->value)]
final class OauthSiteRedirectCRUDController extends AppAbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SiteRedirect::class;
    }
}
