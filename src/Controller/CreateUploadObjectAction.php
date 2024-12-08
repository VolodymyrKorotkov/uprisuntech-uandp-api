<?php

namespace App\Controller;

use App\Entity\FileUpload;
use App\Service\FileUploader\Dto\FileUploaderDto;
use App\Service\FileUploader\FileUploader;
use App\Service\FileUploader\FileUploaderEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class CreateUploadObjectAction extends AbstractController
{
    private FileUploader $fileUploader;

    public function __construct(
        FileUploader $fileUploader
    )
    {
        $this->fileUploader = $fileUploader;
    }

    public function __invoke(Request $request): FileUpload
    {
        return $this->fileUploader->upload(new FileUploaderDto(
            provider: FileUploaderEnum::S3Bucket,
            file: $request->files->get('file')
        ));

    }
}