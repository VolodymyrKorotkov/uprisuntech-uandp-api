<?php

namespace App\Controller;

use App\Service\FileUploader\Dto\DownloadDataDto;
use App\Service\FileUploader\Dto\DownloadDto;
use App\Service\FileUploader\FileUploader;
use App\Service\FileUploader\FileUploaderEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class DownloadUploadObjectAction extends AbstractController
{
    private FileUploader $fileUploader;

    public function __construct(
        FileUploader $fileUploader
    )
    {
        $this->fileUploader = $fileUploader;
    }

    public function __invoke(string $identifier): Response
    {
        return $this->makeResponse(
            $this->fileUploader->download(
                new DownloadDto(
                    provider: FileUploaderEnum::S3Bucket,
                    identifier: $identifier
                )
            )
        );

    }

    public function makeResponse(DownloadDataDto $dto): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($dto->fileName) . '"');
        $response->setContent($dto->file);
        return $response;
    }
}