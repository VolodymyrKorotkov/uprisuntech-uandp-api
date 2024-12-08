<?php

namespace App\Service\FileUploader;

use App\Entity\FileUpload;
use App\Repository\FileUploadRepository;
use App\Service\FileUploader\ConcreteFileUploaders\SystemFileUploaderInterface;
use App\Service\FileUploader\Dto\DownloadDataDto;
use App\Service\FileUploader\Dto\DownloadDto;
use App\Service\FileUploader\Dto\FileUploaderDto;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FileUploader
{
    private array $providers;
    private FileUploadRepository $fileUploadRepository;
    private Security $security;

    public function __construct(
        /**
         * @var $providers array<SystemFileUploaderInterface>
         */
        #[TaggedIterator(SystemFileUploaderInterface::class)] iterable $providers,
        FileUploadRepository $fileUploadRepository,
        Security $security
    )
    {
        foreach ($providers as $provider) {
            $this->providers[$provider->getType()->value] = $provider;
        }
        $this->fileUploadRepository = $fileUploadRepository;
        $this->security = $security;
    }

    public function upload(FileUploaderDto $dto): FileUpload
    {
        $filePath = $this->providers[$dto->provider->value]->upload($dto->file);
        $fileUpload = new FileUpload();
        $fileUpload->setPath($filePath);
        $fileUpload->setIdentifier(pathinfo($filePath)['filename']);
        $fileUpload->setUploadByIdentifier('test');
        $this->fileUploadRepository->save($fileUpload);
        return $fileUpload;
    }

    public function download(DownloadDto $dto): DownloadDataDto
    {
        $path = $this->fileUploadRepository->findPathByIdentifier($dto->identifier);
        if (!$path) {
            throw new NotFoundHttpException('File not found');
        }

        return $this->providers[$dto->provider->value]->download($path);
    }
}