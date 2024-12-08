<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\CamundaStrategy;
use App\Enum\EntityCrudRoleEnum;
use App\Enum\UserRoleEnum;
use App\Form\CamundaTaskFilterType;
use App\Repository\FormIoRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class CamundaStrategyCrudController extends AppAbstractCrudController
{
    public function __construct(
        private FormIoRepository $formIoRepository
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return CamundaStrategy::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id')->hideOnForm();
        yield TextField::new('title');
        yield TextField::new('alias')->hideOnForm();
        yield TextField::new('role');
        yield TextField::new('tenantId');
        yield TextField::new('camundaAlias');
        yield BooleanField::new('allowStartProcess');
        yield BooleanField::new('enabled');
        yield AssociationField::new('defaultForm')->autocomplete();
        yield AssociationField::new('tableForm')->autocomplete();

        yield CollectionField::new('filters', 'Filters')
            ->setHelp('Example for property: "assigneeIn" or "assigneeLike". See https://docs.camunda.org/rest/camunda-bpm-platform/7.21-SNAPSHOT/#tag/Task/operation/getTasks. Example for value: "{userIdentity},{userRoles}"')
            ->setEntryIsComplex(true)
            ->setEntryType(CamundaTaskFilterType::class)
            ->setFormTypeOption('allow_delete', true)
            ->setFormTypeOption('allow_add', true)
            ->hideOnIndex();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setPageTitle('edit', 'Edit type')
            ->setPageTitle('new', 'Create type')
            ->setPageTitle('index', 'Application types');
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)->update(Crud::PAGE_INDEX, Action::NEW, fn(Action $act) => $act->setLabel('Create type'));
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

    /**
     * @return array|object[]
     */
    public function getFormChoiceGenerator(): array
    {
        $result = [];
        foreach ($this->formIoRepository->findAll() as $form){
            $result[(string)$form] = $form;
        }

        return $result;
    }
}
