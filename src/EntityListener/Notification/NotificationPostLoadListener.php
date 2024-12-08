<?php declare(strict_types=1);

namespace App\EntityListener\Notification;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\ORM\NonUniqueResultException;

#[AsEntityListener(event: Events::postLoad, method: 'postLoad', entity: Notification::class)]
final readonly class NotificationPostLoadListener
{

    public function __construct(
        private NotificationRepository $notificationRepository,
    )
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function postLoad(Notification $notification): void
    {
        $notification->setViewed(true);
        $this->notificationRepository->save($notification);
    }
}
