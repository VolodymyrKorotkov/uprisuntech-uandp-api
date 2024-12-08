<?php declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

#[AsEventListener(event: RequestEvent::class)]
final readonly class QueryParamsAsCamelCaseRequestListener
{
    public function __invoke(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        foreach ($request->query->all() as $param => $value){
            $paramCamelCase = $this->snakeToCamelCase($param);
            if ($request->query->has($paramCamelCase)){
                continue;
            }

            $request->query->set($paramCamelCase, $value);
        }
    }

    private function snakeToCamelCase(string $input): string
    {
        return \lcfirst(\str_replace('_', '', \ucwords($input, '_')));
    }
}
