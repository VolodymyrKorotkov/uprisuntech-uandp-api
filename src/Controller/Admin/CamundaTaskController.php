<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Enum\AppRouteNameEnum;
use App\Enum\AppRoutePrefixEnum;
use App\Enum\UserRoleEnum;
use App\Repository\ApplicationTypeRepository;
use App\Service\AggregateProcessStarter\StartAggregateProcessTaskDto;
use App\Service\AggregateProcessStarter\TaskSourcesAggregateStarter;
use App\Service\AggregateTaskProvider\CamundaTaskProviderAdapter;
use App\Service\AggregateTaskProvider\Dto\GetOneTaskFromAllSourcesDto;
use App\Service\CamundaProcessStarter\CamundaProcessStarter;
use App\Service\CamundaTaskAssigner\CamundaTaskAuthUserAssigner;
use App\Service\CamundaTaskCompleter\CamundaTaskCompleter;
use App\Service\CamundaTaskCompleter\CompleteCamundaTaskDto;
use App\Service\CamundaTaskProvider\CamundaTaskByFilterProviderInterface;
use App\Service\CamundaTaskProvider\GetTaskListFilterDto;
use Doctrine\ORM\EntityNotFoundException;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

#[IsGranted(UserRoleEnum::ROLE_CAMUNDA_TASK->value)]
#[Route(AppRoutePrefixEnum::ADMIN->value)]
final class CamundaTaskController extends AbstractController
{
    public function __construct(
        private readonly CamundaTaskByFilterProviderInterface $taskSecurityFilterProvider,
        private readonly CamundaTaskCompleter                 $taskCompleter,
        private readonly AdminUrlGenerator                    $adminUrlGenerator
    )
    {
    }

    #[Route('/camunda-tasks/{typeId}', name: AppRouteNameEnum::ADMIN_CAMUNDA_TASKS_ROUTE->value)]
    public function getTasks(int $typeId, ApplicationTypeRepository $typeRepository): Response
    {
        $type = $typeRepository->getById($typeId);

        $tasks = $this->taskSecurityFilterProvider->getTasks(
            new GetTaskListFilterDto(
                typeId: $typeId
            )
        );

        return $this->render('admin_camunda_tasks.html.twig', [
            'tasks' => $tasks,
            'type' => $type
        ]);
    }

    /**
     * @throws Throwable
     */
    #[Route('/camunda-tasks/{typeId}/start', name: 'admin.camunda_tasks.start')]
    public function startProcess(int $typeId, TaskSourcesAggregateStarter $processStarter): Response
    {
        try {
            $startDto = new StartAggregateProcessTaskDto();
            $startDto->setTypeId($typeId);

            $processStarter->startAggregateProcess($startDto);
        } catch (EntityNotFoundException) {
            throw $this->createNotFoundException();
        }

        return $this->redirectToTasks($typeId);
    }

    #[Route('/camunda-tasks/{typeId}/{id}/assign-me', name: 'admin.camunda_tasks.assign_me')]
    public function assignMe(int $typeId, string $id, CamundaTaskAuthUserAssigner $assigner): Response
    {
        $assigner->assignForAuthUser($id);

        return $this->redirect(
            $this->generateSubmissionUrl($typeId, $id)
        );
    }

    /**
     * @throws Throwable
     */
    #[Route('/camunda-tasks/{typeId}/{id}/submission/{submissionId}', name: AppRouteNameEnum::ADMIN_CAMUNDA_TASKS_SUBMISSION_ROUTE->value)]
    public function taskSubmission(
        string $id,
        int $typeId,
        CamundaTaskProviderAdapter $camundaTaskProviderAdapter,
        string|null $submissionId = null,
    ): Response
    {
        $filter = new GetOneTaskFromAllSourcesDto();
        $filter->setTaskId($id);
        $task = $camundaTaskProviderAdapter->getOneSourceTask($filter);
        $submissionId = $task->submissionId;

        return $this->render('admin_camunda_tasks_submission_view.html.twig', [
            'task' => $task,
            'typeId' => $typeId,
            'completeUrl' => $this->generateCompleteUrl($typeId, $id),
            'assignMeUrl' => $this->generateAssignMeUrl($typeId, $id),
            'submissionId' => $submissionId
        ]);
    }

    /**
     * @throws Throwable
     * @throws EntityNotFoundException
     */
    #[Route('/camunda-tasks/{typeId}/{id}/complete', name: 'admin.camunda_tasks.complete')]
    public function taskComplete(int $typeId, string $id): Response
    {
        try {
            $this->taskCompleter->completeCamundaTask(
                new CompleteCamundaTaskDto($id)
            );
        } catch (BadRequestHttpException $exception) {
            $this->addFlash('danger', $exception->getMessage());
        }

        return $this->redirectToTasks($typeId);
    }

    private function redirectToTasks(int $typeId): RedirectResponse
    {
        return $this->redirect(
            $this->adminUrlGenerator->setRoute(
                AppRouteNameEnum::ADMIN_CAMUNDA_TASKS_ROUTE->value,
                ['typeId' => $typeId]
            )->generateUrl()
        );
    }

    /**
     * @param string $id
     * @return string
     */
    private function generateCompleteUrl(int $typeId, string $id): string
    {
        return $this->adminUrlGenerator->setRoute(
            AppRouteNameEnum::ADMIN_CAMUNDA_TASKS_COMPLETE_ROUTE->value,
            ['id' => $id, 'typeId' => $typeId]
        )->generateUrl();
    }

    /**
     * @param int $typeId
     * @param string $id
     * @param string|null $submissionId
     * @return string
     */
    public function generateSubmissionUrl(int $typeId, string $id, string|null $submissionId = null): string
    {
        return $this->adminUrlGenerator->setRoute(
            AppRouteNameEnum::ADMIN_CAMUNDA_TASKS_SUBMISSION_ROUTE->value,
            [
                'typeId' => $typeId,
                'id' => $id,
                'submissionId' => $submissionId
            ]
        )->generateUrl();
    }

    private function generateAssignMeUrl(int $typeId, string $id): string
    {
        return $this->adminUrlGenerator->setRoute(
            AppRouteNameEnum::ADMIN_CAMUNDA_TASKS_ASSIGN_ME_ROUTE->value,
            [
                'typeId' => $typeId,
                'id' => $id
            ]
        )->generateUrl();
    }
}
