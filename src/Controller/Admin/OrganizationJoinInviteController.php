<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppAbstractCrudController;
use App\Entity\OrganizationJoinInvite;
use App\Enum\OrganizationJoinInviteStatusEnum;
use App\Enum\UserRoleEnum;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class OrganizationJoinInviteController extends AppAbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OrganizationJoinInvite::class;
    }

    public static function getViewRole(): UserRoleEnum
    {
        return UserRoleEnum::ROLE_ORGANIZATION_JOIN_INVITE;
    }

    public static function getNewRole(): UserRoleEnum
    {
        return UserRoleEnum::ROLE_ORGANIZATION_JOIN_INVITE;
    }

    public static function getEditRole(): UserRoleEnum
    {
        return UserRoleEnum::ROLE_ORGANIZATION_JOIN_INVITE;
    }

    public static function getDeleteRole(): UserRoleEnum
    {
        return UserRoleEnum::ROLE_ORGANIZATION_JOIN_INVITE;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id')->hideOnForm();
        yield TextField::new('user')->hideOnForm();
        yield DateTimeField::new('updatedAt')->hideOnForm();
        yield DateTimeField::new('createdAt')->hideOnForm();
        yield TextField::new('inviteUrl')
            ->formatValue(fn($r) => '<a href="'.$r.'" target="_blank">Invite link</a>')
            ->hideOnForm();
        yield TextField::new('drfoCode');
        yield EmailField::new('email');
        yield AssociationField::new('organization')->hideOnForm();
        yield TextField::new('jobTitle');
        yield TextField::new('fullName');
        yield TextField::new('phone');

        yield ChoiceField::new('status')
            ->setChoices(OrganizationJoinInviteStatusEnum::cases())
            ->hideOnForm();
        yield TextField::new('comment')->hideOnForm();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['updatedAt' => 'DESC']);
    }
}
