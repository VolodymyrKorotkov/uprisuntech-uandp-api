<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Enum\AppRoutePrefixEnum;
use App\Repository\NotificationRepository;
use App\Security\EntityHasOwnerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            paginationEnabled: true,
            paginationClientEnabled: true,
            paginationClientItemsPerPage: true,
        ),
    ],
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    normalizationContext: ['groups' => [self::SAFE_GROUP]],
)]
#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification implements EntityHasOwnerInterface
{
    const SAFE_GROUP = 'safe';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([self::SAFE_GROUP])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([self::SAFE_GROUP])]
    private ?string $userIdentity = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([self::SAFE_GROUP])]
    private ?string $message = null;

    #[ORM\Column]
    #[Groups([self::SAFE_GROUP])]
    private bool $viewed = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserIdentity(): ?string
    {
        return $this->userIdentity;
    }

    public function setUserIdentity(string $userIdentity): static
    {
        $this->userIdentity = $userIdentity;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function isViewed(): ?bool
    {
        return $this->viewed;
    }

    public function setViewed(bool $viewed): static
    {
        $this->viewed = $viewed;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentity;
    }
}
