<?php

namespace App\Entity;

use ApiPlatform\Metadata\Delete;
use App\Enum\AppRoutePrefixEnum;
use App\Enum\UserRoleEnum;
use App\Repository\OrganizationUserRoleRepository;
use App\Security\EntityHasOwnerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[Delete(
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
)]
#[ORM\Entity(repositoryClass: OrganizationUserRoleRepository::class)]
#[UniqueEntity(fields: ['organization', 'userIdentifier'])]
class OrganizationUserRole implements EntityHasOwnerInterface
{
    const SAFE_GROUP = 'safe';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Organization $organization = null;

    #[ORM\ManyToOne]
    #[Groups([self::SAFE_GROUP])]
    private ?User $user = null;

    #[ORM\Column(enumType: UserRoleEnum::class)]
    #[Groups([self::SAFE_GROUP])]
    private UserRoleEnum $role = UserRoleEnum::ROLE_MUNICIPALITY_MANAGER_CASE;

    #[ORM\Column(name: '"default"')]
    #[Groups([self::SAFE_GROUP])]
    private bool $default = false;

    public function __toString(): string
    {
        return $this->role->value.' - '.$this->getUser();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->getOrganization()->getUserIdentifier();
    }

    public function getRole(): UserRoleEnum
    {
        return $this->role;
    }

    public function setRole(UserRoleEnum $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): static
    {
        $this->organization = $organization;

        return $this;
    }

    public function isDefault(): ?bool
    {
        return $this->default;
    }

    public function setDefault(bool $default): static
    {
        $this->default = $default;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function hasUser(): bool
    {
        return null !== $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
