<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\Organization;
use App\Entity\Sub\DataTimesFieldsTrait;
use App\Enum\UserRoleEnum;
use App\Form\OrganizationUserRoleType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

final class OrganizationCrudController extends AppAbstractCrudController
{
    use DataTimesFieldsTrait;

    public static function getEntityFqcn(): string
    {
        return Organization::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermission(Action::EDIT, UserRoleEnum::ROLE_SUPER_ADMIN)
            ->setPermission(Action::NEW, UserRoleEnum::ROLE_SUPER_ADMIN)
            ->setPermission(Action::DELETE, UserRoleEnum::ROLE_SUPER_ADMIN);
    }

    public function configureFields(string $pageName): iterable
    {
        foreach (parent::configureFields($pageName) as $configureField) {
            yield $configureField;
        }

        yield DateTimeField::new('createdAt')->hideOnForm();
        yield AssociationField::new('user', 'Owner')->setPermission(UserRoleEnum::ROLE_SUPER_ADMIN);
        yield CollectionField::new('roles', 'Roles')
            ->setEntryType(OrganizationUserRoleType::class)
            ->setFormTypeOption('allow_delete', true)
            ->setFormTypeOption('allow_add', true)
            ->hideOnIndex();
    }

    protected function getExcludeDefaultProperties(): array
    {
        return ['default', 'updatedAt', 'createdAt'];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['updatedAt' => 'DESC']);
    }

    public static function getViewRole(): UserRoleEnum
    {
        return UserRoleEnum::ROLE_ORGANIZATION;
    }
}
