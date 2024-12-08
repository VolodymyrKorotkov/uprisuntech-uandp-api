<?php declare(strict_types=1);

namespace App\Entity;

use App\Enum\UserRoleEnum;
use App\Validator\UniqueOrganizationTitle;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

#[Entity]
class OrganizationJoinFlowData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups([OrganizationJoinFlow::GROUP_SAVE, OrganizationJoinTask::GROUP_SAVE, OrganizationJoinFlow::GROUP_VIEW, OrganizationJoinTask::GROUP_VIEW])]
    #[Length(8)]
    #[Regex(pattern: '/^[0-9]\d*$/', message: 'Please use only positive numbers.')]
    #[NotBlank]
    #[ORM\Column]
    private ?string $edrpou;

    #[Groups([OrganizationJoinTask::GROUP_SAVE, OrganizationJoinTask::GROUP_VIEW])]
    #[ORM\Column(nullable: true, enumType: UserRoleEnum::class)]
    private ?UserRoleEnum $role = UserRoleEnum::ROLE_MUNICIPALITY_HEAD_CASE;

    #[UniqueOrganizationTitle]
    #[Groups([OrganizationJoinTask::GROUP_SAVE, OrganizationJoinTask::GROUP_VIEW])]
    #[ORM\Column]
    private ?string $title = '';

    public function getEdrpou(): ?string
    {
        return $this->edrpou;
    }

    public function setEdrpou(?string $edrpou): void
    {
        $this->edrpou = $edrpou;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getRole(): ?UserRoleEnum
    {
        return $this->role;
    }

    public function setRole(?UserRoleEnum $role): void
    {
        $this->role = $role;
    }
}
