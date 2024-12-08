<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\CreateUploadObjectAction;
use App\Controller\DownloadUploadObjectAction;
use App\Enum\FileStorageRouteEnum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;

#[Post(
    uriTemplate: '/upload',
    inputFormats: ['multipart'],
    outputFormats: ['json'],
    routePrefix: FileStorageRouteEnum::ACCOUNT_PREFIX->value,
    controller: CreateUploadObjectAction::class,
    normalizationContext: ['groups' => self::GET_FILE],
    denormalizationContext: ['groups' => self::UPLOAD_FILE],
    deserialize: false,
)]
#[Get(
    uriTemplate: '/{identifier}',
    outputFormats: ['octet_stream'],
    routePrefix: FileStorageRouteEnum::ACCOUNT_PREFIX->value,
    controller: DownloadUploadObjectAction::class,
)]
#[ORM\Entity]
class FileUpload
{
    public const GET_FILE = 'getFile';
    public const UPLOAD_FILE = 'uploadFile';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ApiProperty(identifier: false)]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups([self::GET_FILE])]
    #[ApiProperty(identifier: true)]
    #[ORM\Column(length: 255)]
    private ?string $identifier = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\Column(length: 255)]
    private ?string $uploadByIdentifier = null;

    #[Groups([self::UPLOAD_FILE])]
    public File $file;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getUploadByIdentifier(): ?string
    {
        return $this->uploadByIdentifier;
    }

    public function setUploadByIdentifier(string $uploadByIdentifier): static
    {
        $this->uploadByIdentifier = $uploadByIdentifier;

        return $this;
    }

    public function setFile(?File $file = null): void
    {
        $this->file = $file;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }
}