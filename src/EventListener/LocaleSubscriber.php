<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    /**
     * @return array<string, array<int[]|string[]>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $acceptLanguage = $request->headers->get('accept-language');
        if (empty($acceptLanguage)) {
            return;
        }

        $arr = HeaderUtils::split($acceptLanguage, ',;');
        if (empty($arr[0][0])) {
            return;
        }

        // Symfony expects underscore instead of dash in locale
        $locale = str_replace('-', '_', $arr[0][0]);

        $request->setLocale($locale);
    }
}
