<?php

namespace App\Service\FileUploader\Dto;

class DownloadDataDto
{
    public function __construct(
        public string $fileName,
        public string $file,
    )
    {
    }
}