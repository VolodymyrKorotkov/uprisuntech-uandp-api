<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Enum\DescBookRouteEnum;
use App\Repository\GuideBookRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GuideBookRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: self::SERIALIZED_CONTEXT_COLLECTION,
        ),
        new Post(),
        new Put(),
        new Get(),
        new Delete(),
    ],
    routePrefix: DescBookRouteEnum::ADMIN_PREFIX->value,
    normalizationContext: self::SERIALIZED_CONTEXT_ITEM,
    denormalizationContext: self::SERIALIZED_CONTEXT_ITEM
)]
#[ApiFilter(BooleanFilter::class, properties: ['enable'])]
#[ApiFilter(NumericFilter::class, properties: ['parent.id'])]
#[ApiFilter(SearchFilter::class, properties: ['title' => 'partial'])]
#[ApiFilter(ExistsFilter::class, properties: ['parent'])]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class GuideBook  implements TranslatableInterface
{
    use TranslatableTrait;

    private const SAFE_GROUP = 'safe';
    private const ASSOC_GROUP = 'guide_book';
    private const SERIALIZED_CONTEXT_ITEM = ['groups' => [self::SAFE_GROUP, self::ASSOC_GROUP]];
    private const SERIALIZED_CONTEXT_COLLECTION = ['groups' => [self::SAFE_GROUP]];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(self::SAFE_GROUP)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(self::SAFE_GROUP)]
    private ?string $title = '';

    #[ORM\ManyToOne(targetEntity: GuideBook::class, inversedBy: 'guideBooks')]
    private ?GuideBook $parent = null;

    #[Groups(self::ASSOC_GROUP)]
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: GuideBook::class, cascade: ['All'], orphanRemoval: true )]
    private Collection $guideBooks;

    #[Groups(self::SAFE_GROUP)]
    #[ORM\Column(options: ['default' => false])]
    #[Assert\NotBlank]
    private ?bool $enable = null;

    #[Groups(self::ASSOC_GROUP)]
    #[ApiProperty(readable: true, writable: false)]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $deletedAt;

    public function __construct()
    {
        $this->guideBooks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->translate()
        ->getTitle();
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getGuideBooks(): Collection
    {
        return $this->guideBooks;
    }


    public function addGuideBook(self $guideBook): static
    {
        if (!$this->guideBooks->contains($guideBook)) {
            $this->guideBooks->add($guideBook);
            $guideBook->setParent($this);
        }

        return $this;
    }

    public function removeGuideBook(self $guideBook): static
    {
        if ($this->guideBooks->removeElement($guideBook)) {
            // set the owning side to null (unless already changed)
            if ($guideBook->getParent() === $this) {
                $guideBook->setParent(null);
            }
        }

        return $this;
    }

    public function isEnable(): ?bool
    {
        return $this->enable;
    }

    public function setEnable(bool $enable): static
    {
        $this->enable = $enable;

        return $this;
    }

    public function getDeletedAt(): DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeInterface $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function __toString(){
        return $this->title; //or anything else
    }
}
