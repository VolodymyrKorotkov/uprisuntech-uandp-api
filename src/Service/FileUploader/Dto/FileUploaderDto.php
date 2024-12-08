<?php

namespace App\Service\FileUploader\Dto;

use App\Service\FileUploader\FileUploaderEnum;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class FileUploaderDto
{
    public function __construct(
        public FileUploaderEnum $provider,
        #[Assert\NotBlank]
        #[Assert\File(maxSize: '20m')]
        public UploadedFile $file,
    )
    {
    }
}