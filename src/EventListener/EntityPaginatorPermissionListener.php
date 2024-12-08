<?php declare(strict_types=1);

namespace App\EventListener;

use ApiPlatform\Doctrine\Orm\Paginator;
use App\Enum\EntityCrudVoteAttributeEnum;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ViewEvent;

#[AsEventListener(event: self::EVENT_NAME, method: self::METHOD_NAME, priority: self::EVENT_PRIORITY)]
final class EntityPaginatorPermissionListener extends AbstractEntityPermissionListener
{
    protected function getEditorVoterAttribute(ViewEvent $event): EntityCrudVoteAttributeEnum
    {
        return EntityCrudVoteAttributeEnum::VIEW_ATTRIBUTE;
    }

    protected function support(ViewEvent $event): bool
    {
        return $event->getControllerResult() instanceof Paginator;
    }

    protected function getResultObjects(ViewEvent $event): iterable
    {
        $result = [];
        foreach ($event->getControllerResult() as $item){
            $result[] = $item;
        }

        return $result;
    }
}
