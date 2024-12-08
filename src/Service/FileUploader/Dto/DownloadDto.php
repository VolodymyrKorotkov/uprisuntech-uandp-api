<?php

namespace App\Service\FileUploader\Dto;

use App\Service\FileUploader\FileUploaderEnum;

class DownloadDto
{
    public function __construct(
        public FileUploaderEnum $provider,
        public string $identifier,
    )
    {
    }
}