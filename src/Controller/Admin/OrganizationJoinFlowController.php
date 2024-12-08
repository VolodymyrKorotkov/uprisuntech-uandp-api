<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\OrganizationJoinFlow;
use App\Enum\EntityCrudRoleEnum;
use App\Enum\OrganizationJoinStatusEnum;
use App\Enum\UserRoleEnum;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class OrganizationJoinFlowController extends AppAbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OrganizationJoinFlow::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id')
            ->hideWhenCreating()
            ->hideWhenUpdating();

        yield TextField::new('user')
            ->hideWhenUpdating()
            ->hideWhenCreating();

        yield DateTimeField::new('updatedAt')
            ->hideWhenCreating()
            ->hideWhenUpdating();

        yield DateTimeField::new('createdAt')
            ->hideWhenCreating()
            ->hideWhenUpdating();

        yield TextField::new('title')->hideOnForm();
        yield TextField::new('edrpou')->hideOnIndex();

        yield ChoiceField::new('status')
            ->setChoices(OrganizationJoinStatusEnum::cases())
            ->hideWhenUpdating()
            ->hideWhenCreating();
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::DELETE, Action::EDIT);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['updatedAt' => 'DESC']);
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
}
