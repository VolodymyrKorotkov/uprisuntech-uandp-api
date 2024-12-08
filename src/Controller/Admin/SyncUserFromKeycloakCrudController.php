<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\SyncUserFromKeycloak;
use App\Enum\UserRoleEnum;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(UserRoleEnum::ROLE_SUPER_ADMIN_CASE->value)]
final class SyncUserFromKeycloakCrudController extends AppAbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SyncUserFromKeycloak::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id')->hideOnForm();
        yield EmailField::new('email');
        yield AssociationField::new('user')->hideOnForm();
        yield DateTimeField::new('createdAt')->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->disable(
                Action::EDIT, Action::DELETE
            );
    }
}
