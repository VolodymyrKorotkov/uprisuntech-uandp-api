<?php declare(strict_types=1);

namespace App\Service\AggregateTaskProvider;

use App\Repository\ApplicationTypeRepository;
use App\Service\AggregateTaskProvider\Dto\GetOneTaskFromAllSourcesDto;
use App\Service\AggregateTaskProvider\Dto\GetOneTskFromAllSourcesResult;
use App\Service\AggregateTaskProvider\Dto\ProcessTasksCollection;
use App\Service\AggregateTaskProvider\Dto\TasksFromAllSourcesFilterDto;
use App\Service\AggregateTaskProvider\Dto\TasksSourceFilterDto;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class TaskSourcesAggregateProvider implements TaskSourcesAggregateProviderInterface
{
    /**
     * @param iterable<TaskProviderAdapterInterface> $providers
     * @param ApplicationTypeRepository $applicationTypeRepository
     * @param Security $security
     */
    public function __construct(
        #[TaggedIterator(TaskProviderAdapterInterface::class)] private iterable $providers,
        private ApplicationTypeRepository                                       $applicationTypeRepository,
        private Security                                                        $security
    )
    {
    }

    /**
     * @throws Exception
     */
    public function getTasksFromAllSources(TasksFromAllSourcesFilterDto $dto): ProcessTasksCollection
    {
        $filter = new TasksSourceFilterDto(
            applicationType: $this->applicationTypeRepository->getById($dto->getTypeId()),
            user: $this->security->getUser(),
            offset: $dto->getOffset(),
            itemsPerPage: $dto->getItemsPerPage()
        );

        foreach ($this->providers as $provider) {
            if ($provider->getStrategyType() === $filter->getApplicationType()->getStrategyType()) {
                return new ProcessTasksCollection(
                    tasks: fn() => $provider->getSourceTasks($filter),
                    count: $provider->getCount($filter)
                );
            }
        }

        throw new Exception('Provider not found');
    }

    public function getOneTaskFromAllSources(GetOneTaskFromAllSourcesDto $dto): GetOneTskFromAllSourcesResult
    {
        foreach ($this->providers as $provider){
            try {
                return new GetOneTskFromAllSourcesResult(
                    task: $provider->getOneSourceTask($dto),
                    strategyType: $provider->getStrategyType()
                );
            } catch (NotFoundHttpException){
                continue;
            }
        }

        throw new NotFoundHttpException('Task not found!');
    }
}
