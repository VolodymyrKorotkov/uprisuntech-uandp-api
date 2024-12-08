<?php

namespace App\Service\FileUploader;

enum FileUploaderEnum :string
{
    case Local = 'local';
    case S3Bucket = 's3bucket';
}
