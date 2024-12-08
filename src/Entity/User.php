<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\ApiPlatform\AbstractObjectPopulateResolver;
use App\Controller\ApiPlatform\ConfirmEmail\ConfirmEmailProcessor;
use App\Controller\ApiPlatform\ConfirmEmail\Dto\ConfirmEmailDto;
use App\Controller\ApiPlatform\ConfirmEmail\Dto\SendConfirmPasswordDto;
use App\Controller\ApiPlatform\ConfirmEmail\SendConfirmEmailProcessor;
use App\Controller\ApiPlatform\EmailPasswordInit\Dto\EmailPasswordInitDto;
use App\Controller\ApiPlatform\EmailPasswordInit\EmailPasswordInitProcessor;
use App\Controller\ApiPlatform\Me\CurrentUserObjectResolver;
use App\Controller\ApiPlatform\Me\CurrentUserProvider;
use App\Controller\ApiPlatform\ResetPassword\Dto\ResetPasswordDto;
use App\Controller\ApiPlatform\ResetPassword\Dto\SendResetPasswordDto;
use App\Controller\ApiPlatform\ResetPassword\ResetPasswordProcessor;
use App\Controller\ApiPlatform\ResetPassword\SendResetPasswordProcessor;
use App\Controller\ApiPlatform\UpdatePassword\Dto\UpdatePassword;
use App\Controller\ApiPlatform\UpdatePasswordProcessor;
use App\Enum\AppRoutePrefixEnum;
use App\Repository\UserRepository;
use App\Security\EntityHasOwnerInterface;
use App\Service\KeycloakClient\Dto\KeycloakUser;
use App\Service\OauthGovIdProvider\Dto\GovIdResourceOwnerDto;
use App\Service\OauthKeycloakProvider\KeycloakResourceOwner;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Google\Service\Oauth2\Userinfo;
use JetBrains\PhpStorm\Deprecated;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

#[Post()]
#[Patch(
    uriTemplate: '/users/{uuid}',
)]
#[Delete()]
#[GetCollection()]
#[Get()]

#[Get(
    uriTemplate: '/users/me',
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    normalizationContext: ['groups' => self::SAFE_GROUP],
    provider: CurrentUserProvider::class
)]
#[Put(
    uriTemplate: '/users/me',
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    denormalizationContext: [
        AbstractObjectPopulateResolver::CONTEXT_FIELD => CurrentUserObjectResolver::class
    ]
)]
#[Patch(
    uriTemplate: '/users/me/update-password',
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    input: UpdatePassword::class,
    output: false,
    processor: UpdatePasswordProcessor::class
)]
#[Post(
    uriTemplate: '/users/me/confirm-email/send',
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    input: SendConfirmPasswordDto::class,
    output: false,
    provider: CurrentUserProvider::class,
    processor: SendConfirmEmailProcessor::class
)]
#[Post(
    uriTemplate: '/users/me/confirm-email',
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    input: ConfirmEmailDto::class,
    output: false,
    provider: CurrentUserProvider::class,
    processor: ConfirmEmailProcessor::class
)]
#[Post(
    uriTemplate: '/users/me/email-password-init',
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    input: EmailPasswordInitDto::class,
    output: false,
    processor: EmailPasswordInitProcessor::class
)]
#[Post(
    uriTemplate: '/users/reset-password/send',
    routePrefix: AppRoutePrefixEnum::API_PUBLIC->value,
    input: SendResetPasswordDto::class,
    output: false,
    processor: SendResetPasswordProcessor::class
)]
#[Post(
    uriTemplate: '/users/reset-password',
    routePrefix: AppRoutePrefixEnum::API_PUBLIC->value,
    input: ResetPasswordDto::class,
    output: false,
    processor: ResetPasswordProcessor::class
)]
#[GetCollection(
    routePrefix: AppRoutePrefixEnum::API_ACCOUNT->value,
    normalizationContext: ['groups' => self::SAFE_GROUP],
    denormalizationContext: ['groups' => self::SAFE_GROUP],
    security: "is_granted('ROLE_USERS_LIST')",
)]

#[ApiFilter(SearchFilter::class, properties: ['uuid' => 'exact'])]
#[ORM\HasLifecycleCallbacks()]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table("`user`")]
#[UniqueEntity(['email'])]
#[UniqueEntity(['edrpouCode'])]
#[UniqueEntity(['drfoCode'])]
class User implements EntityHasOwnerInterface, IgnoreEntityOwnerViewPermission
{
    public const SAFE_GROUP = 'safe';
    public const USERNAME_FIELD = 'uuid';

    #[ApiProperty(identifier: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    //todo: rename uuid
    #[Deprecated]
    #[Uuid]
    #[Groups(self::SAFE_GROUP)]
    #[ORM\Column(type: "guid")]
    #[ApiProperty(identifier: true)]
    private ?string $uuid = null;

    #[Groups([self::SAFE_GROUP])]
    #[Email]
    #[ORM\Column(type: "string", unique: true, nullable: true)]
    private ?string $email = null;

    #[NotBlank]
    #[Groups([self::SAFE_GROUP])]
    #[ORM\Column(type: "string", nullable: true)]
    private ?string $name = null;

    #[NotBlank]
    #[Groups([self::SAFE_GROUP])]
    #[ORM\Column(type: "string", nullable: true)]
    private string|null $lastname = null;

    #[Groups([self::SAFE_GROUP])]
    #[ORM\Column(type: "string", nullable: true)]
    private string|null $middleName = null;

    #[Groups([self::SAFE_GROUP])]
    #[ORM\Column(type: "string", nullable: true)]
    private string|null $phone = null;

    #[Groups(self::SAFE_GROUP)]
    #[ORM\Column(type: "string", unique: true, nullable: true)]
    private string|null $edrpouCode = null;

    #[Groups([self::SAFE_GROUP])]
    #[ORM\Column(type: "string", unique: true, nullable: true)]
    private string|null $drfoCode = null;

    #[Groups(self::SAFE_GROUP)]
    #[ORM\Column(type: "string", nullable: true)]
    private string|null $gender = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $code = null;

    #[Groups(self::SAFE_GROUP)]
    #[ORM\Column(type: "date", nullable: true)]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[Groups(self::SAFE_GROUP)]
    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $passwordUpdatedDate = null;

    #[Groups(self::SAFE_GROUP)]
    #[ORM\Column(type: "string", nullable: true)]
    private ?string $accountType = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $resetPasswordHash = null;

    #[ORM\Column(type: "boolean", nullable: false, options: ['default' => false])]
    private ?bool $isVerifiedEmail = false;

    #[ORM\Column(type: "boolean", nullable: false, options: ['default' => false])]
    private ?bool $isCompany = false;

    #[ORM\Column(type: "boolean", nullable: false, options: ['default' => false])]
    private ?bool $hasPassword = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $confirmEmailHash = null;
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: SyncUserFromKeycloak::class, cascade: ['all'])]
    private Collection $syncLogs;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserJwtToken::class, cascade: ['all'])]
    private Collection $jwtTokens;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ApplicationProcess::class, cascade: ['all'])]
    private Collection $processes;

    public function __construct()
    {
        $this->syncLogs = new ArrayCollection();
        $this->jwtTokens = new ArrayCollection();
        $this->processes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getFullName() ?? $this->email;
    }

    public function getFullName(): string
    {
        return trim($this->lastname . ' ' . $this->name . ' ' . $this->middleName);
    }

    public function fillFromGoogleData(Userinfo $dto): self
    {
        $this->email = $dto->email;
        $this->name = $this->name ?? $dto->givenName;
        $this->lastname = $this->lastname ?? $dto->familyName;
        $this->gender = $this->gender ?? $dto->gender;
        $this->drfoCode = $this->drfoCode ?? uniqid();
        $this->edrpouCode = $this->edrpouCode ?? uniqid();

        return $this;
    }

    public function fillFromGovUaData(GovIdResourceOwnerDto $dto): self
    {
        $this->email = $dto->email;
        $this->name = $dto->givenname;
        $this->lastname = $dto->lastname;
        $this->middleName = $dto->middlename;
        $this->isCompany = (bool)$dto->edrpoucode;
        $this->edrpouCode = $dto->edrpoucode;
        $this->drfoCode = $dto->drfocode;
        $this->gender = $dto->gender;
        $this->phone = $dto->phone;

        return $this;
    }

    public function updateFromGovUaData(GovIdResourceOwnerDto $dto): static
    {
        $this->lastname = $dto->lastname;

        $this->drfoCode = $dto->drfocode;
        $this->edrpouCode = $dto->edrpoucode;

        $this->isCompany = (bool)$dto->edrpoucode;

        return $this;
    }

    public function fillFromKeycloakData(KeycloakResourceOwner $dto): static
    {
        $this->setUserIdentifier($dto->getId());

        $this->email = $this->email ?? $dto->getEmail();
        $this->name = $this->name ?? $dto->getFirstName();
        $this->lastname = $this->lastname ?? $dto->getLastName();

        $jwt = new UserJwtToken();
        $jwt->setJwtToken($dto->getJwt());
        $jwt->setRefreshToken($dto->getRefreshToken());
        $this->addJwtToken($jwt);

        return $this;
    }

    public function rewriteFromKeycloakUser(KeycloakUser $keycloakUser): static
    {
        $this->setUserIdentifier($keycloakUser->id);
        $this->email = $keycloakUser->email;
        $this->name = $keycloakUser->firstName;
        $this->lastname = $keycloakUser->lastName;
        $this->isVerifiedEmail = $keycloakUser->emailVerified;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    #[SerializedName('userIdentifier')]
    public function getUserIdentifier(): string
    {
        return $this->uuid;
    }

    public function hasUserIdentifier(): bool
    {
        return null !== $this->uuid;
    }

    public function setUserIdentifier(string $userIdentity): static
    {
        $this->uuid = $userIdentity;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function generateCode(): void
    {
        $this->code = md5(random_bytes(20));
    }

    public function removeCode(): void
    {
        $this->code = null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    #[Deprecated]
    public function getUuid(): string
    {
        return $this->uuid;
    }

    #[Deprecated]
    public function setUuid(?string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function hasEmail(): bool
    {
        return null !== $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): static
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEdrpouCode(): ?string
    {
        return $this->edrpouCode;
    }

    public function setEdrpouCode(?string $edrpouCode): static
    {
        $this->edrpouCode = $edrpouCode;
        return $this;
    }

    public function getDrfoCode(): ?string
    {
        return $this->drfoCode;
    }

    public function setDrfoCode(?string $drfoCode): static
    {
        $this->drfoCode = $drfoCode;
        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getResetPasswordHash(): ?string
    {
        return $this->resetPasswordHash;
    }

    public function setResetPasswordHash(?string $resetPasswordHash): self
    {
        $this->resetPasswordHash = $resetPasswordHash;
        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }

    public function getAccountType(): ?string
    {
        return $this->accountType;
    }

    public function setAccountType(?string $accountType): self
    {
        $this->accountType = $accountType;
        return $this;
    }

    public function getPasswordUpdatedDate(): ?\DateTimeInterface
    {
        return $this->passwordUpdatedDate;
    }

    public function setPasswordUpdatedDate(?\DateTimeInterface $passwordUpdatedDate): self
    {
        $this->passwordUpdatedDate = $passwordUpdatedDate;
        return $this;
    }

    public function isIsVerifiedEmail(): ?bool
    {
        return $this->isVerifiedEmail;
    }

    public function setIsVerifiedEmail(bool $isVerifiedEmail): static
    {
        $this->isVerifiedEmail = $isVerifiedEmail;

        return $this;
    }

    public function isCompany(): ?bool
    {
        return $this->isCompany;
    }

    public function setIsCompany(bool $isCompany): static
    {
        $this->isCompany = $isCompany;

        return $this;
    }

    #[Groups(self::SAFE_GROUP)]
    public function getHasPassword(): ?bool
    {
        return $this->hasPassword;
    }

    public function setHasPassword(bool $hasPassword): static
    {
        $this->hasPassword = $hasPassword;

        return $this;
    }

    public function getConfirmEmailHash(): ?string
    {
        return $this->confirmEmailHash;
    }

    public function setConfirmEmailHash(?string $confirmEmailHash): static
    {
        $this->confirmEmailHash = $confirmEmailHash;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function isIsCompany(): ?bool
    {
        return $this->isCompany;
    }

    /**
     * @return Collection<int, SyncUserFromKeycloak>
     */
    public function getSyncLogs(): Collection
    {
        return $this->syncLogs;
    }

    public function addSyncLog(SyncUserFromKeycloak $syncLog): static
    {
        if (!$this->syncLogs->contains($syncLog)) {
            $this->syncLogs->add($syncLog);
            $syncLog->setUser($this);
        }

        return $this;
    }

    public function removeSyncLog(SyncUserFromKeycloak $syncLog): static
    {
        if ($this->syncLogs->removeElement($syncLog)) {
            // set the owning side to null (unless already changed)
            if ($syncLog->getUser() === $this) {
                $syncLog->setUser(null);
            }
        }

        return $this;
    }

    public function getLastJwtToken(): UserJwtToken
    {
        return $this->jwtTokens->last();
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function isHasPassword(): ?bool
    {
        return $this->hasPassword;
    }

    /**
     * @return Collection<int, UserJwtToken>
     */
    public function getJwtTokens(): Collection
    {
        return $this->jwtTokens;
    }

    public function addJwtToken(UserJwtToken $jwtToken): static
    {
        if (!$this->jwtTokens->contains($jwtToken)) {
            $this->jwtTokens->add($jwtToken);
            $jwtToken->setUser($this);
        }

        return $this;
    }

    public function removeJwtToken(UserJwtToken $jwtToken): static
    {
        if ($this->jwtTokens->removeElement($jwtToken)) {
            // set the owning side to null (unless already changed)
            if ($jwtToken->getUser() === $this) {
                $jwtToken->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ApplicationProcess>
     */
    public function getProcesses(): Collection
    {
        return $this->processes;
    }

    public function addProcess(ApplicationProcess $process): static
    {
        if (!$this->processes->contains($process)) {
            $this->processes->add($process);
            $process->setUser($this);
        }

        return $this;
    }

    public function removeProcess(ApplicationProcess $process): static
    {
        if ($this->processes->removeElement($process)) {
            // set the owning side to null (unless already changed)
            if ($process->getUser() === $this) {
                $process->setUser(null);
            }
        }

        return $this;
    }
}
