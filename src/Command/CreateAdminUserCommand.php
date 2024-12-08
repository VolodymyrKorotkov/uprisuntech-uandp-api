<?php declare(strict_types=1);

namespace App\Command;

use App\Enum\UserRoleEnum;
use App\Service\KeycloakClient\Dto\CreateUserDto;
use App\Service\KeycloakClient\KeycloakAdminClientInterface;
use App\Service\KeycloakUserProvider\KeycloakUniqueUserProviderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class CreateAdminUserCommand extends Command
{
    public function __construct(
        private readonly KeycloakAdminClientInterface  $keycloakUserCrudClient,
        #[Autowire('%env(KEYCLOAK_ADMIN)%')]
        private readonly string                        $keycloakAdminUsername,
        #[Autowire('%env(KEYCLOAK_ADMIN_PASSWORD)%')]
        private readonly string                        $keycloakAdminPassword,
        private readonly KeycloakUniqueUserProviderInterface $keycloakUserProvider
    )
    {
        parent::__construct('app:create_admin');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->keycloakUserCrudClient->createUser(
            new CreateUserDto(
                password: $this->keycloakAdminPassword,
                username: $this->keycloakAdminUsername,
                firstName: 'Super',
                lastName: 'Admin'
            )
        );

        $keycloakUser = $this->keycloakUserProvider->getByUsername($this->keycloakAdminUsername);
        $this->keycloakUserCrudClient->updateUserRolesRealmMapping(
            $keycloakUser->id,
            $this->getRoles()
        );

        (new SymfonyStyle($input, $output))->success('Success!!');

        return 0;
    }

    /**
     * @return array
     */
    private function getRoles(): array
    {
        $superAdminRole = $this->keycloakUserCrudClient->getRoleByName(
            UserRoleEnum::ROLE_SUPER_ADMIN_CASE->value,
        );
        unset($superAdminRole['attributes']);

        $adminRole = $this->keycloakUserCrudClient->getRoleByName(
            UserRoleEnum::ROLE_ADMIN->value,
        );
        unset($adminRole['attributes']);

        return [$superAdminRole, $adminRole];
    }
}
