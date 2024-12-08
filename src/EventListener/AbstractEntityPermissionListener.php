<?php declare(strict_types=1);

namespace App\EventListener;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use App\Entity\IgnoreCheckEntityCrudPermission;
use App\Entity\IgnoreEntityOwnerViewPermission;
use App\Enum\EntityCrudVoteAttributeEnum;
use App\Enum\UserRoleEnum;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\MappingException;
use ReflectionException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

abstract class AbstractEntityPermissionListener
{
    protected const METHOD_NAME = 'kernelViewHandle';
    protected const EVENT_NAME = KernelEvents::VIEW;
    public const EVENT_PRIORITY = EventPriorities::PRE_VALIDATE;

    public function __construct(
        private Security $security,
        private EntityManagerInterface $em
    ){}

    /**
     * @throws ReflectionException
     */
    public function kernelViewHandle(ViewEvent $event): void
    {
        if (!$this->support($event)){
            return;
        }

        if (!$this->security->getUser()) {
            return;
        }
        if ($this->security->isGranted(UserRoleEnum::ROLE_SUPER_ADMIN)) {
            return;
        }

        foreach ($this->getResultObjects($event) as $object) {
            $this->checkObject($object, $event);
        }
    }

    /**
     * @param $object
     * @param ViewEvent $event
     * @return void
     * @throws ReflectionException
     */
    public function checkObject($object, ViewEvent $event): void
    {
        if ($object instanceof IgnoreCheckEntityCrudPermission) {
            return;
        }

        $attribute = $this->getVoterAttribute($event);
        if ($attribute === EntityCrudVoteAttributeEnum::VIEW_ATTRIBUTE && $object instanceof IgnoreEntityOwnerViewPermission){
            return;
        }
        if ($this->isViewNotPersistedEntityAction($attribute, $object)){
            return;
        }

        if ($attribute === EntityCrudVoteAttributeEnum::VIEW_ATTRIBUTE && $this->em->getMetadataFactory()->isTransient(get_class($object))){
            return;
        }

        $isGranted = $this->security->isGranted($attribute->value, $object);

        if (!$isGranted) {
            throw new AccessDeniedException(
                message: 'You do not have access to this resource - ' . (new \ReflectionClass($object))->getShortName() . '(' . $object->{'getId'}() . ')'
            );
        }
    }

    private function getVoterAttribute(ViewEvent $event): EntityCrudVoteAttributeEnum
    {
        if ($event->getRequest()->isMethod(Request::METHOD_GET)) {
            return EntityCrudVoteAttributeEnum::VIEW_ATTRIBUTE;
        } else {
            return $this->getEditorVoterAttribute($event);
        }
    }

    /**
     * @throws MappingException
     */
    private function isViewNotPersistedEntityAction(EntityCrudVoteAttributeEnum $attr, object $object): bool
    {
        if ($attr !== EntityCrudVoteAttributeEnum::VIEW_ATTRIBUTE){
            return false;
        }

        if ($this->em->getMetadataFactory()->isTransient(get_class($object))){
            return false;
        }

        return false === $this->em->contains($object);
    }

    abstract protected function getEditorVoterAttribute(ViewEvent $event): EntityCrudVoteAttributeEnum;
    abstract protected function support(ViewEvent $event): bool;
    abstract protected function getResultObjects(ViewEvent $event): iterable;
}
