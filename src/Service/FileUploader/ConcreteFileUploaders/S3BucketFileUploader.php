<?php

namespace App\Service\FileUploader\ConcreteFileUploaders;

use App\Service\FileUploader\Dto\DownloadDataDto;
use App\Service\FileUploader\FileUploaderEnum;
use Aws\S3\S3Client;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class S3BucketFileUploader implements SystemFileUploaderInterface
{
    public function __construct(
        #[Autowire(env: 'S3_ENDPOINT')]
        private readonly string $endpoint,
        #[Autowire(env: 'S3_BUCKET')]
        private readonly string $bucket,
        #[Autowire(env: 'S3_VERSION')]
        private readonly string $version,
        #[Autowire(env: 'S3_REGION')]
        private readonly string $region,
        #[Autowire(env: 'S3_KEY')]
        private readonly string $key,
        #[Autowire(env: 'S3_SECRET')]
        private readonly string $secret

    )
    {
    }

    public function upload(UploadedFile $file): string
    {
        $filename = uniqid() . '.' . pathinfo($file->getClientOriginalName())['extension'];
        $client = $this->getClient();
        $client->upload(
            $this->bucket,
            $filename,
            $file->getContent()
        );
        return $this->bucket . '/' . $filename;
    }

    public function download(string $path): DownloadDataDto
    {
        $pathInfo = pathinfo($path);
        $client = $this->getClient();
        $result = $client->getObject([
            'Bucket' => $pathInfo['dirname'],
            'Key' => $pathInfo['basename']
        ]);
        return new DownloadDataDto(
            fileName: $pathInfo['basename'],
            file: $result['Body']
        );
    }

    public function getType(): FileUploaderEnum
    {
        return FileUploaderEnum::S3Bucket;
    }

    private function getClient(): S3Client
    {
        return new S3Client([
            'version' => $this->version,
            'region' => $this->region,
            'endpoint' => $this->endpoint,
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => $this->key,
                'secret' => $this->secret
            ]
        ]);
    }
}