<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\ApplicationTask;
use App\Enum\ApplicationStrategyEnum;
use App\Enum\AppRouteNameEnum;
use App\Enum\EntityCrudRoleEnum;
use App\Enum\UserRoleEnum;
use App\Service\AggregateTaskAssigner\AggregateTaskAssigner;
use App\Service\AggregateTaskAssigner\AssignTaskDto;
use App\Service\NativeProcessStarter\ApplicationTaskStarterInterface;
use App\Service\NativeTaskCompleter\NativeTaskCompletePermissionChecker;
use App\Service\NativeTaskCompleter\NativeTaskCompleterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin')]
final class ApplicationTaskCrudController extends AppAbstractCrudController
{
    public function __construct(
        private readonly ApplicationTaskStarterInterface           $applicationTaskStarter,
        private readonly NativeTaskCompleterInterface              $applicationTaskCompleter,
        private readonly NativeTaskCompletePermissionChecker       $completePermissionChecker,
        private readonly AdminUrlGenerator                         $adminUrlGenerator
    )
    {
    }

    #[Route('/native-tasks/{id}/assign-me', name: AppRouteNameEnum::ADMIN_NATIVE_TASKS_ASSIGN_ME_ROUTE->value)]
    public function assignMe(ApplicationTask $task, AggregateTaskAssigner $assigner): Response
    {
        $dto = new AssignTaskDto();
        $dto->setId($task->getTaskId());

        $assigner->assignTask($dto);

        return $this->redirect(
            $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::EDIT)
                ->setEntityId($task->getId())
                ->generateUrl()
        );
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param ApplicationTask $entityInstance
     * @return void
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->applicationTaskStarter->startApplicationTask($entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::updateEntity($entityManager, $entityInstance);
        $this->applicationTaskCompleter->completeTask($entityInstance);
    }

    public static function getEntityFqcn(): string
    {
        return ApplicationTask::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id')
            ->hideWhenCreating()
            ->hideWhenUpdating();

        yield IntegerField::new('taskId')
            ->hideWhenCreating()
            ->hideWhenUpdating();

        yield IntegerField::new('processInstanceId')
            ->hideWhenCreating()
            ->hideWhenUpdating();

        yield TextField::new('title')
            ->hideOnForm();

        yield AssociationField::new('type')->autocomplete()->setQueryBuilder(
            $this->getQueryBuilderCallableForNativeType()
        )
            ->hideWhenUpdating()->hideOnIndex();

        yield TextField::new('userIdentifier', 'Assigner')->hideOnForm();
        yield TextField::new('role', 'Role')->hideOnForm();
        yield DateTimeField::new('updatedAt')
            ->hideOnForm();
        yield DateTimeField::new('createdAt')
            ->hideOnForm();

        yield BooleanField::new('completed')->setDisabled()->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->disable(Action::SAVE_AND_CONTINUE, Action::SAVE_AND_ADD_ANOTHER)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)

            ->add(Crud::PAGE_INDEX, $this->createTaskAssignAction()->displayAsLink())
            ->add(Crud::PAGE_EDIT, $this->createTaskAssignAction()->displayAsLink())
            ->add(Crud::PAGE_DETAIL, $this->createTaskAssignAction()->displayAsLink())

            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $this->displayIfCanEditComplete($action);
            })
            ->update(Crud::PAGE_DETAIL, Action::EDIT, function (Action $action) {
                return $this->displayIfCanEditComplete($action);
            })
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, function (Action $action) {
                $action->setLabel('Complete');
                return $this->displayIfCanEditComplete($action);
            })
            ->update(Crud::PAGE_INDEX, Action::NEW, fn(Action $action) => $action->setLabel('Create new Task'));
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['completed' => 'ASC', 'updatedAt' => 'DESC'])
            ->overrideTemplate('crud/detail', 'admin_application_task_detail.html.twig')
            ->overrideTemplate('crud/edit', 'admin_application_task_edit.html.twig')
            ->setPageTitle('edit', fn(ApplicationTask $task) => (string)$task)
            ->setPageTitle('new', 'Create new Task')
            ->setPageTitle('index', 'Tasks');
    }

    /**
     * @param Action $action
     * @return Action
     */
    function displayIfCanEditComplete(Action $action): Action
    {
        return $action->displayIf(fn(ApplicationTask $task) => $this->completePermissionChecker->canCompleteTask($task));
    }

    public static function getViewRole(): UserRoleEnum
    {
        return EntityCrudRoleEnum::newFromEntityClass(self::getEntityFqcn())->getRoleView();
    }

    protected static function getNewRole(): UserRoleEnum
    {
        return EntityCrudRoleEnum::newFromEntityClass(self::getEntityFqcn())->getRoleCreate();
    }

    protected static function getEditRole(): UserRoleEnum
    {
        return EntityCrudRoleEnum::newFromEntityClass(self::getEntityFqcn())->getRoleEdit();
    }

    protected static function getDeleteRole(): UserRoleEnum
    {
        return EntityCrudRoleEnum::newFromEntityClass(self::getEntityFqcn())->getRoleDelete();
    }

    public function configureAssets(Assets $assets): Assets
    {
        return parent::configureAssets($assets)
//            ->addCssFile(
//                Asset::new('https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css')->ignoreOnIndex()
//            )
            ->addCssFile(
                Asset::new('https://cdn.form.io/formiojs/formio.full.min.css')->ignoreOnIndex()
            )
            ->addJsFile(
                Asset::new('https://cdn.form.io/formiojs/formio.full.min.js')->ignoreOnIndex()
            );
    }

    /**
     * @return \Closure
     */
    private function getQueryBuilderCallableForNativeType(): \Closure
    {
        return fn(QueryBuilder $queryBuilder) => $queryBuilder
            ->andWhere('entity.strategyType = :nativeStrategy')
            ->andWhere('entity.allowStartProcess = true')
            ->setParameter('nativeStrategy', ApplicationStrategyEnum::NATIVE);
    }

    private function createTaskAssignAction(): Action
    {
        return Action::new('assignNativeTask', 'Assign')
            ->linkToRoute(
                AppRouteNameEnum::ADMIN_NATIVE_TASKS_ASSIGN_ME_ROUTE->value,
                fn(ApplicationTask $task) => ['id' => $task->getId()]
            )
            ->displayIf(fn(ApplicationTask $task) => $this->completePermissionChecker->canAssign($task));
    }
}
