<?php

namespace App\EntityListener;

use App\Entity\SyncDataMessageInterface;
use App\Enum\ActionTypeEnum;
use App\Messenger\EntityDataSyncMessage;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[AsDoctrineListener(event: Events::postPersist, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::postUpdate, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::preRemove, priority: 500, connection: 'default')]
final readonly class EntitySyncDataListener
{
    public function __construct(
        private MessageBusInterface $messageInterface,
        private SerializerInterface $serializer
    )
    {
    }

    public function postPersist(LifecycleEventArgs $args): void
    {

        if ($this->canSendThisEntity($args->getObject())) {
            $serializedData = $this->serializer->serialize($args->getObject(), 'json', ['groups' => SyncDataMessageInterface::SERIALIZED_FIELD]);
            $this->sendMessage(new EntityDataSyncMessage(
                action: ActionTypeEnum::CREATE->value,
                data: json_decode($serializedData, true)
            ));
        }
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        if ($this->canSendThisEntity($args->getObject())) {
            $this->sendMessage(new EntityDataSyncMessage(
                action: ActionTypeEnum::UPDATE->value,
                data: json_decode($this->serializer->serialize($args->getObject(), 'json', ['groups' => SyncDataMessageInterface::SERIALIZED_FIELD]), true)
            ));
        }
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        if ($this->canSendThisEntity($args->getObject())) {
            $this->sendMessage(new EntityDataSyncMessage(
                action: ActionTypeEnum::DELETE->value,
                data: json_decode($this->serializer->serialize($args->getObject(), 'json', ['groups' => SyncDataMessageInterface::SERIALIZED_FIELD]), true)
            ));
        }
    }

    protected function sendMessage(EntityDataSyncMessage $message): void
    {
        $this->messageInterface->dispatch($message);
    }

    private function canSendThisEntity($model): bool
    {
        return $model instanceof SyncDataMessageInterface;
    }
}