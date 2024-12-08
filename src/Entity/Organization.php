<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Entity\Sub\DataTimesFieldsTrait;
use App\Enum\AppRoutePrefixEnum;
use App\Repository\OrganizationRepository;
use App\Security\EntityHasOwnerInterface;
use App\Service\OrganizationProvider\MeDefaultOrganizationApiProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;

#[Get(
    uriTemplate: 'organization/default',
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    normalizationContext: ['groups' => [self::SAFE_GROUP]],
    provider: MeDefaultOrganizationApiProvider::class,
)]
#[UniqueEntity('title')]
#[UniqueEntity('edrpou')]
#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Organization implements EntityHasOwnerInterface, IgnoreEntityOwnerViewPermission
{
    use DataTimesFieldsTrait;

    const SAFE_GROUP = 'safe';
    const TITLE_LENGTH = 50;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([self::SAFE_GROUP])]
    private ?int $id = null;

    #[Length(max: self::TITLE_LENGTH)]
    #[ORM\Column(length: self::TITLE_LENGTH, unique: true)]
    #[Groups([self::SAFE_GROUP])]
    private ?string $title = null;

    #[ORM\Column(unique: true)]
    #[Groups([self::SAFE_GROUP])]
    private ?string $edrpou = null;

    #[ORM\Column(name: '"default"')]
    private bool $default = false;

    #[ORM\ManyToOne]
    #[Groups([self::SAFE_GROUP])]
    private ?Organization $parent = null;

    /**
     * @var Collection<Organization>
     */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Organization::class)]
    #[Groups([self::SAFE_GROUP])]
    private Collection $children;

    #[ORM\OneToMany(mappedBy: 'organization', targetEntity: OrganizationUserRole::class, cascade: ['All'])]
    #[Groups([self::SAFE_GROUP])]
    private Collection $roles;

    #[Groups([self::SAFE_GROUP])]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'organization', targetEntity: OrganizationJoinInvite::class, cascade: ['all'])]
    private Collection $invites;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->invites = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->title . ' (' . $this->edrpou . ')';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getEdrpou(): ?string
    {
        return $this->edrpou;
    }

    public function setEdrpou(string $edrpou): static
    {
        $this->edrpou = $edrpou;

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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function haseParent(): bool
    {
        return null !== $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, OrganizationUserRole>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(OrganizationUserRole $role): static
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
            $role->setOrganization($this);
        }

        return $this;
    }

    public function removeRole(OrganizationUserRole $role): static
    {
        if ($this->roles->removeElement($role)) {
            // set the owning side to null (unless already changed)
            if ($role->getOrganization() === $this) {
                $role->setOrganization(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Organization>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Organization $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Organization $child): static
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getUserIdentifier(): string
    {
        return $this->user?->getUserIdentifier() ?? '';
    }

    /**
     * @return Collection<int, OrganizationJoinInvite>
     */
    public function getInvites(): Collection
    {
        return $this->invites;
    }

    public function addInvite(OrganizationJoinInvite $invite): static
    {
        if (!$this->invites->contains($invite)) {
            $this->invites->add($invite);
            $invite->setOrganization($this);
        }

        return $this;
    }

    public function removeInvite(OrganizationJoinInvite $invite): static
    {
        if ($this->invites->removeElement($invite)) {
            // set the owning side to null (unless already changed)
            if ($invite->getOrganization() === $this) {
                $invite->setOrganization(null);
            }
        }

        return $this;
    }
}
