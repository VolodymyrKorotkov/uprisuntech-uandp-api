<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\OrganizationJoinTask;
use App\Enum\OrganizationJoinStatusEnum;
use App\Enum\UserRoleEnum;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class OrganizationJoinTaskController extends AppAbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OrganizationJoinTask::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id')
            ->hideWhenCreating()
            ->hideWhenUpdating();

        yield TextField::new('flow.user', 'User')
            ->hideWhenUpdating()
            ->hideWhenCreating();

        yield DateTimeField::new('updatedAt')
            ->hideWhenCreating()
            ->hideWhenUpdating();

        yield DateTimeField::new('createdAt')
            ->hideWhenCreating()
            ->hideWhenUpdating();

        yield TextField::new('title')->hideOnIndex();
        yield TextField::new('edrpou')->hideOnIndex();
        yield ChoiceField::new('role')->setChoices(UserRoleEnum::getOrganizationRoles())->hideOnIndex();

        yield ChoiceField::new('status')
            ->setChoices(OrganizationJoinStatusEnum::cases());
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->disable(Action::DELETE, Action::NEW);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['updatedAt' => 'DESC']);
    }

    public static function getViewRole(): UserRoleEnum
    {
        return UserRoleEnum::ROLE_ORGANIZATION_JOIN_TASK;
    }

    public static function getEditRole(): UserRoleEnum
    {
        return UserRoleEnum::ROLE_ORGANIZATION_JOIN_TASK;
    }
}
