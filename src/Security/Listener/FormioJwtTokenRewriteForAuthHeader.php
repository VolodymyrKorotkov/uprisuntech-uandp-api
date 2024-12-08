<?php declare(strict_types=1);

namespace App\Security\Listener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

#[AsEventListener(event: RequestEvent::class, method: 'handleRequest', priority: 9)]
#[AsEventListener(event: ResponseEvent::class, method: 'handleResponse')]
final readonly class FormioJwtTokenRewriteForAuthHeader
{
    public function handleRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $xJwtToken = $request->headers->get('X-Jwt-Token');
        $authToken = str_replace('Bearer ', '', $request->headers->get('Authorization', ''));
        if ($xJwtToken && !$authToken){
            $request->headers->set('Authorization', 'Bearer '. $xJwtToken);
        }
    }

    public function handleResponse(ResponseEvent $event): void
    {
        $xJwtToken = $event->getRequest()->headers->get('X-Jwt-Token');
        if ($xJwtToken){
            $response = $event->getResponse();
            $response->headers->set('X-Jwt-Token', $xJwtToken);
        }
    }
}
