<?php declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\FormIo;
use App\Service\FormIoClient\Dto\GetFormDto;
use App\Service\FormIoClient\FormIoClient;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Throwable;

#[AsEntityListener(event: Events::preUpdate, method: 'handle', entity: Formio::class)]
#[AsEntityListener(event: Events::prePersist, method: 'handle', entity: Formio::class)]
final readonly class FormioSetIdAndTitleByApiListener
{
    public function __construct(
        private FormIoClient $formIoClient
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function handle(Formio $formIo): void
    {
        $formMetadata = $this->formIoClient->getForm(
            new GetFormDto($formIo->getFormKey())
        );
        $formIo->setTitle($formMetadata->title);
        $formIo->setFormId($formMetadata->id);
    }
}