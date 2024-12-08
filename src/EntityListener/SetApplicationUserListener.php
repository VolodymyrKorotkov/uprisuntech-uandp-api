<?php declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Sub\EntityHasApplicationUserInterface;
use App\Security\ApplicationUserSecurity;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsDoctrineListener(event: Events::prePersist)]
final readonly class SetApplicationUserListener
{
    public function __construct(
        private ApplicationUserSecurity $security,
    )
    {}

    public function prePersist(LifecycleEventArgs $args): void
    {
        if (!$this->security->isUserAuth()){
            return;
        }

        $entity = $args->getObject();
        if (!$entity instanceof EntityHasApplicationUserInterface){
            return;
        }

        if (!$entity->hasUser()){
            $entity->setUser($this->security->getUser());
        }
    }
}
