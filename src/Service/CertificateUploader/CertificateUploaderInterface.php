<?php
namespace App\Service\CertificateUploader;

use App\Entity\Certificate;
use App\Service\CertificateUploader\Dto\CertificateDto;
use Symfony\Component\HttpFoundation\File\File;

interface CertificateUploaderInterface
{
    public function createOrUpdateCertificate(CertificateDto $dto, ?File $file): Certificate;
}
