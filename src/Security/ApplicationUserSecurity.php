<?php declare(strict_types=1);

namespace App\Security;

use App\Entity\Organization;
use App\Entity\User;
use App\Repository\OrganizationRepository;
use App\Service\AppUserProvider;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class ApplicationUserSecurity
{
    public function __construct(
        private Security               $security,
        private OrganizationRepository $organizationRepository,
        private AppUserProvider        $userProvider,
        private JWTTokenManagerInterface $JWTTokenManager
    )
    {
    }

    public function getUser(): User
    {
        return $this->userProvider->getUser(
            $this->security->getUser()->getUserIdentifier()
        );
    }

    public function getJwtToken(): string
    {
        $user = $this->security->getUser();

        return $this->JWTTokenManager->createFromPayload(
            $user,
            AppUser::getPayload(
                $user->getUserIdentifier(),
                $user->getRoles()
            )
        );
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getDefaultOrganizationAsOwner(): Organization
    {
        return $this->organizationRepository->getDefaultOrganizationAsMunicipalityHead(
            $this->security->getUser()->getUserIdentifier()
        );
    }

    public function isUserAuth(): bool
    {
        return null !== $this->security->getUser();
    }
}
