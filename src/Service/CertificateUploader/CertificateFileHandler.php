<?php

namespace App\Service\CertificateUploader;

use App\Entity\Certificate;
use App\Repository\CertificateRepository;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;

class CertificateFileHandler
{
    public function __construct(
        private PropertyMappingFactory $mappingFactory,
        private CertificateRepository $certificateRepository
    ){}

    public function addFile(Certificate $certificate, ?File $file): Certificate
    {
        if(!$file){
            return $certificate;
        }

        $certificate->setFile($file);
        $this->certificateRepository->save($certificate);

        $uriPrefix = $this->getCertificateFileUriPrefix($certificate);
        $certificate->setCertificateFile(sprintf('%s/%s', $uriPrefix, $certificate->getCertificateFile()));

        return $certificate;
    }

    public function removeUriPrefixInFileName(Certificate $certificate): Certificate
    {
        return $certificate->setCertificateFile(
            str_replace($this->getCertificateFileUriPrefix($certificate), '', $certificate->getCertificateFile())
        );
    }

    private function getCertificateFileUriPrefix(Certificate $certificate): string
    {
        $mapping = $this->mappingFactory->fromField($certificate, 'file');

        return $mapping->getUriPrefix();
    }
}