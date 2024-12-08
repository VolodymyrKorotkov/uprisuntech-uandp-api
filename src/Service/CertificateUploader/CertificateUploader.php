<?php
namespace App\Service\CertificateUploader;

use App\Entity\Certificate;
use App\Repository\CertificateRepository;
use App\Service\CertificateUploader\Dto\CertificateDto;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\File;

class CertificateUploader implements CertificateUploaderInterface
{
    public function __construct(
        private Security $security,
        private CertificateRepository $certificateRepository,
        private CertificateFileHandler $fileHandler
    ){}

    public function createOrUpdateCertificate(CertificateDto $dto, ?File $file): Certificate
    {
        $certificate = !$dto->id
            ? $this->createCertificate($dto)
            : $this->updateCertificate($dto);

        $certificate = $this->fileHandler->addFile($certificate, $file);
        $this->certificateRepository->save($certificate);

        return $certificate;
    }

    private function createCertificate(CertificateDto $dto): Certificate
    {
        $certificate = Certificate::createFromDto($dto);
        $user = $this->security->getUser();


        $certificate->setUserName($user->getUserIdentifier());

        return $certificate;
    }

    private function updateCertificate(CertificateDto $dto): Certificate
    {
        $certificate = $this->certificateRepository->findUserCertificateById($this->security->getUser(), $dto->id);
        if(!$certificate){
            return $this->createCertificate($dto);
        }

        $certificate->updateFromDto($dto);
        $certificate = $this->fileHandler->removeUriPrefixInFileName($certificate);

        return $certificate;
    }
}
