<?php
namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener(event: 'kernel.exception')]
class ExceptionListener
{
	public function __construct(private LoggerInterface $logger)
	{
	}

	public function onKernelException(ExceptionEvent $event): void
	{
		$exception = $event->getThrowable();
		$this->logger->error($exception->getMessage(), ['exception' => $exception]);
	}
}
