<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\OrganizationUserRole;
use App\Enum\UserRoleEnum;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class OrganizationUserRoleController extends AppAbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OrganizationUserRole::class;
    }

    public function configureFields(string $pageName): iterable
    {
       yield AssociationField::new('organization')->hideOnForm();
       yield TextField::new('user');
       yield ChoiceField::new('role')->setChoices(UserRoleEnum::cases())->hideOnForm();
       yield BooleanField::new('default');
    }

    public static function getViewRole(): UserRoleEnum
    {
        return UserRoleEnum::ROLE_ORGANIZATION_ROLE;
    }

    protected static function getDeleteRole(): UserRoleEnum
    {
        return UserRoleEnum::ROLE_ORGANIZATION_ROLE_DELETE;
    }
}
