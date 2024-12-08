<?php declare(strict_types=1);

namespace App\Controller\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Resource\ProcessSubmissionVariable;
use App\Service\ProcessSubmissionVariable\ProcessSubmissionVariablesValuesProvider;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

final readonly class CamundaProcessSubmissionProvider implements ProviderInterface
{
    public function __construct(
        private ProcessSubmissionVariablesValuesProvider $variablesValuesProvider,
    )
    {
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws EntityNotFoundException
     * @throws Throwable
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ProcessSubmissionVariable
    {
        $processInstanceId = $context['uri_variables']['processInstanceId'];
        if (empty($processInstanceId)) {
            throw new NotFoundHttpException('instanceId is empty');
        }

        return $this->variablesValuesProvider->getProcessVariables($processInstanceId);
    }
}
