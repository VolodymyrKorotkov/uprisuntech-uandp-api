<?php
namespace App\Controller;

use App\Entity\Certificate;
use App\Service\CertificateUploader\CertificateUploaderInterface;
use App\Service\CertificateUploader\Dto\CertificateDto;
use Symfony\Component\HttpFoundation\Request;

class CertificateUploadController extends AbstractAppController
{
    public function __construct(
        private CertificateUploaderInterface $certificateUploader
    ){}

    public function __invoke(Request $request, int $id = null): Certificate
    {
        $dto = CertificateDto::mapToDto($request, $id);

        return $this->certificateUploader->createOrUpdateCertificate($dto, $request->files->get('file'));
    }
}