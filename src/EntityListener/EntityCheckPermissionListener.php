<?php declare(strict_types=1);

namespace App\EntityListener;

use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use App\Entity\IgnoreCheckEntityCrudPermission;
use App\Entity\IgnoreEntityOwnerViewPermission;
use App\Enum\EntityCrudVoteAttributeEnum;
use App\Enum\UserRoleEnum;
use EasyCorp\Bundle\EasyAdminBundle\Event\AbstractLifecycleEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityBuiltEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use ReflectionException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(method: 'beforeEntityPersistedEvent')]
#[AsEventListener(method: 'beforeEntityUpdatedEvent')]
#[AsEventListener(method: 'beforeEntityDeletedEvent')]
#[AsEventListener(method: 'afterEntityBuiltEvent')]
final readonly class EntityCheckPermissionListener
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @throws ReflectionException
     */
    public function afterEntityBuiltEvent(AfterEntityBuiltEvent $event): void
    {
        $subject = $event->getEntity()->getInstance();

        if (empty($subject)){
            return;
        }

        if ($subject instanceof IgnoreEntityOwnerViewPermission){
            return;
        }

        $this->handleSubject(
            subject: $subject,
            attribute: EntityCrudVoteAttributeEnum::VIEW_ATTRIBUTE
        );
    }

    /**
     * @throws ReflectionException
     */
    public function beforeEntityDeletedEvent(BeforeEntityDeletedEvent $event): void
    {
        $this->handleEvent(
            event: $event,
            attribute: EntityCrudVoteAttributeEnum::REMOVE_ATTRIBUTE
        );
    }

    /**
     * @throws ReflectionException
     */
    public function beforeEntityPersistedEvent(BeforeEntityPersistedEvent $event): void
    {
        $this->handleEvent(
            event: $event,
            attribute: EntityCrudVoteAttributeEnum::CREATE_ATTRIBUTE
        );
    }

    /**
     * @throws ReflectionException
     */
    public function beforeEntityUpdatedEvent(BeforeEntityUpdatedEvent $event): void
    {
        $this->handleEvent(
            event: $event,
            attribute: EntityCrudVoteAttributeEnum::EDIT_ATTRIBUTE
        );
    }

    /**
     * @throws ReflectionException
     */
    private function handleEvent(AbstractLifecycleEvent $event, EntityCrudVoteAttributeEnum $attribute): void
    {
        $this->handleSubject($event->getEntityInstance(), $attribute);
    }

    /**
     * @throws ReflectionException
     */
    private function handleSubject(object $subject, EntityCrudVoteAttributeEnum $attribute): void
    {
        if ($this->security->isGranted(UserRoleEnum::ROLE_SUPER_ADMIN_CASE->value)){
            return;
        }

        if ($subject instanceof IgnoreCheckEntityCrudPermission) {
            return;
        }

        $isGranted = $this->security->isGranted($attribute->value, $subject);

        if (!$isGranted) {
            throw new AccessDeniedException(
                message: 'You do not have access to this resource - ' . (new \ReflectionClass($subject))->getShortName() . '(' . $subject->{'getId'}() . ')'
            );
        }
    }
}
