<?php

namespace App\EntityListener;

use App\Service\KeycloakClient\KeycloakClient;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final readonly class UserLogoutListener implements EventSubscriberInterface
{
    public function __construct(
        private KeycloakClient $keycloakClient
    ){}

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }

    public function onLogout(LogoutEvent $event): void
    {
        $username = $event->getToken()?->getUser()?->getUserIdentifier();
        if (!$username){
            return;
        }

        $this->keycloakClient->logout($username);
    }
}
