<?php declare(strict_types=1);

namespace App\EventListener;

use App\Enum\EntityCrudVoteAttributeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\MappingException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;

#[AsEventListener(event: self::EVENT_NAME, method: self::METHOD_NAME, priority: self::EVENT_PRIORITY)]
final class EntityCrudPermissionListener extends AbstractEntityPermissionListener
{
    private EntityManagerInterface $em;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        parent::__construct($security, $em);
        $this->em = $em;
    }

    protected function getEditorVoterAttribute(ViewEvent $event): EntityCrudVoteAttributeEnum
    {
        return match ($event->getRequest()->getMethod()) {
            Request::METHOD_GET => EntityCrudVoteAttributeEnum::VIEW_ATTRIBUTE,
            Request::METHOD_POST => EntityCrudVoteAttributeEnum::CREATE_ATTRIBUTE,
            Request::METHOD_DELETE => EntityCrudVoteAttributeEnum::REMOVE_ATTRIBUTE,
            Request::METHOD_PATCH, Request::METHOD_PUT => EntityCrudVoteAttributeEnum::EDIT_ATTRIBUTE,
        };
    }

    /**
     * @throws MappingException
     */
    protected function support(ViewEvent $event): bool
    {
        $object = $event->getControllerResult();
        if (!is_object($object)) {
            return false;
        }

        return !$this->em->getMetadataFactory()->isTransient($object::class);
    }

    protected function getResultObjects(ViewEvent $event): iterable
    {
        yield $event->getControllerResult();
    }
}
