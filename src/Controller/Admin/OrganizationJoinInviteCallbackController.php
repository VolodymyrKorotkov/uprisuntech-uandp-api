<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Enum\AppRoutePrefixEnum;
use App\Security\KeycloakSecurityUserProvider;
use App\Security\OauthCodeAccountLoginAuthenticator;
use App\Service\OrganizationJoinInviteProcessor\InviteUserStateProcessor;
use App\Service\OrganizationJoinInviteProcessor\ProcessInviteUserStateDto;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;

#[Route(AppRoutePrefixEnum::ADMIN->value)]
final class OrganizationJoinInviteCallbackController extends AbstractController
{
    #[Route('/organization-join/invite/callback')]
    public function callback(
        #[MapQueryString] ProcessInviteUserStateDto $dto,
        InviteUserStateProcessor                    $processor,
        AdminUrlGenerator                           $adminUrlGenerator,
        Security                                    $security,
        KeycloakSecurityUserProvider                $keycloakUserProvider
    ): RedirectResponse
    {
        try {
            $role = $processor->processInviteUserState($dto);

            $url = $adminUrlGenerator
                ->unsetAll()
                ->setController(OrganizationCrudController::class)
                ->setEntityId($role->getOrganization()->getId())
                ->setAction(Action::DETAIL)
                ->generateUrl();

            $this->addFlash('success', 'You have new organization');
            $security->login(
                user: $keycloakUserProvider->loadUserByIdentifier($this->getUser()->getUserIdentifier()),
                authenticatorName: OauthCodeAccountLoginAuthenticator::class
            );

        } catch (\Exception $exception) {
            $this->addFlash('danger', $exception->getMessage());

            $url = $adminUrlGenerator
                ->unsetAll()
                ->setController(OrganizationCrudController::class)
                ->setAction(Action::INDEX)
                ->generateUrl();
        }

        return $this->redirect($url);
    }
}
