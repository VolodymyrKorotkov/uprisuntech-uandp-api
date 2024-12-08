<?php

namespace App\Service\FileUploader\ConcreteFileUploaders;
use App\Service\FileUploader\Dto\DownloadDataDto;
use App\Service\FileUploader\FileUploaderEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[AutoconfigureTag(SystemFileUploaderInterface::class)]
interface SystemFileUploaderInterface
{
    public function upload(UploadedFile $file): string;
    public function download(string $path): DownloadDataDto;
    public function getType(): FileUploaderEnum;
}