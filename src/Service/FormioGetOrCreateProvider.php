<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\FormIo;
use App\Repository\FormIoRepository;
use App\Service\FormIoClient\FormIoClient;
use Doctrine\ORM\EntityNotFoundException;
use Throwable;

final readonly class FormioGetOrCreateProvider
{
    public function __construct(
        private FormIoRepository $formIoRepository,
        private FormIoClient                       $formIoClient,
    )
    {
    }

    public function getOrCreateByFormKey(string $formKey): FormIo
    {
        try {
            return $this->formIoRepository->getByKey($formKey);
        } catch (EntityNotFoundException $e) {
            $formio = new FormIo();
            $formio->setFormKey($formKey);

            $this->formIoRepository->save($formio);

            return $formio;
        }
    }

    /**
     * @throws Throwable
     */
    public function getOrCreateByFormId(string $formId): FormIo
    {
        try {
            return $this->formIoRepository->getByFormId($formId);
        } catch (EntityNotFoundException $e) {
            $form = $this->formIoClient->getFormById($formId);

            $formio = new FormIo();
            $formio->setFormKey($form->path);
            $formio->setTitle($form->title);
            $formio->setFormId($form->id);

            $this->formIoRepository->save($formio);

            return $formio;
        }
    }
}

